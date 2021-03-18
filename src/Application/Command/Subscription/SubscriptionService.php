<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionDTOAssembler;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionSigningDTO;
use Storytale\CustomerActivity\Application\DTOValidation;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionProcessingService;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;
use Storytale\CustomerActivity\PortAdapters\Secondary\Payment\PaymentService;

class SubscriptionService
{
    /** @var SubscriptionProcessingService */
    private SubscriptionProcessingService $subscriptionProcessingService;

    /** @var SubscriptionPlanRepository */
    private SubscriptionPlanRepository $subscriptionPlanRepository;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var DTOValidation */
    private DTOValidation $subscriptionSigningDTOValidation;

    /** @var PaymentService */
    private PaymentService $paymentService;

    /** @var SubscriptionDTOAssembler */
    private SubscriptionDTOAssembler $subscriptionDTOAssembler;

    public function __construct(
        SubscriptionProcessingService $subscriptionProcessingService,
        SubscriptionPlanRepository $subscriptionPlanRepository,
        CustomerRepository $customerRepository,
        DomainSession $domainSession,
        SubscriptionRepository $subscriptionRepository,
        DTOValidation $subscriptionSigningDTOValidation,
        PaymentService $paymentService,
        SubscriptionDTOAssembler $subscriptionDTOAssembler
    )
    {
        $this->subscriptionProcessingService = $subscriptionProcessingService;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->customerRepository = $customerRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionSigningDTOValidation = $subscriptionSigningDTOValidation;
        $this->paymentService = $paymentService;
        $this->subscriptionDTOAssembler = $subscriptionDTOAssembler;
    }

    public function signing(SubscriptionSigningDTO $subscriptionSigningDTO, bool $isActorModerator = false): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->subscriptionSigningDTOValidation->validate($subscriptionSigningDTO);

            $subscriptionPlan = $this->subscriptionPlanRepository->get($subscriptionSigningDTO->getSubscriptionPlanId());
            if (!$subscriptionPlan instanceof SubscriptionPlan) {
                throw new ValidationException('SubscriptionPlan with this id not found.');
            }
            $customer = $this->customerRepository->get($subscriptionSigningDTO->getCustomerId());
            if (!$customer instanceof Customer) {
                throw new ValidationException('Customer with this id not found.');
            }

            try {
                $subscription = $this->subscriptionProcessingService->signing(
                    $subscriptionPlan, $customer, $isActorModerator
                );
            } catch (DomainException $e) {
                throw new ValidationException($e->getMessage());
            }

            if (!$subscription instanceof Subscription) {
                throw new ApplicationException('Can not create subscription. SubscriptionPlan: ' .
                    $subscriptionSigningDTO->getSubscriptionPlanId() . 'Customer: ' . $subscriptionSigningDTO->getCustomerId() . '.');
            }

            $this->subscriptionRepository->save($subscription);
            $this->domainSession->flush();

            $params = [
                'subscription' =>
                    $this->subscriptionDTOAssembler->toArray($subscription),
                'customer' => [
                    'id' => $customer->getId(),
                    'email' => $customer->getEmail(),
                ]
            ];

            $paymentLink = $this->paymentService->getPaymentLink($params);
            $result['subscription']['id'] = $subscription->getId();
            $result['paymentLink'] = $paymentLink;
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }
}