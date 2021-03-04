<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\ApplicationException;
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

    public function __construct(
        SubscriptionProcessingService $subscriptionProcessingService,
        SubscriptionPlanRepository $subscriptionPlanRepository,
        CustomerRepository $customerRepository,
        DomainSession $domainSession,
        SubscriptionRepository $subscriptionRepository,
        DTOValidation $subscriptionSigningDTOValidation
    )
    {
        $this->subscriptionProcessingService = $subscriptionProcessingService;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->customerRepository = $customerRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionSigningDTOValidation = $subscriptionSigningDTOValidation;
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

            $result['subscription']['id'] = $subscription->getId();
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }
}