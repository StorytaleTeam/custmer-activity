<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Subscription\SubscriptionWasCanceledEvent;
use Storytale\Contracts\SharedEvents\Subscription\SubscriptionWasCreatedEvent;
use Storytale\Contracts\SharedEvents\Subscription\SubscriptionWasUnsubscribeEvent;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionHydrator;
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
use Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Paddle\PaddleSubscriptionService;

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

    /** @var SubscriptionHydrator */
    private SubscriptionHydrator $subscriptionHydrator;

    /** @var PaddleSubscriptionService */
    private PaddleSubscriptionService $paddleSubscriptionService;

    /** @var EventBus */
    private EventBus $eventBus;

    public function __construct(
        SubscriptionProcessingService $subscriptionProcessingService,
        SubscriptionPlanRepository $subscriptionPlanRepository,
        CustomerRepository $customerRepository,
        DomainSession $domainSession,
        SubscriptionRepository $subscriptionRepository,
        DTOValidation $subscriptionSigningDTOValidation,
        PaymentService $paymentService,
        SubscriptionHydrator $subscriptionHydrator,
        PaddleSubscriptionService $paddleSubscriptionService,
        EventBus $eventBus
    )
    {
        $this->subscriptionProcessingService = $subscriptionProcessingService;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->customerRepository = $customerRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionSigningDTOValidation = $subscriptionSigningDTOValidation;
        $this->paymentService = $paymentService;
        $this->subscriptionHydrator = $subscriptionHydrator;
        $this->paddleSubscriptionService = $paddleSubscriptionService;
        $this->eventBus = $eventBus;
    }

    public function create(SubscriptionSigningDTO $subscriptionSigningDTO, bool $isActorModerator = false): OperationResponse
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

            $currentSubscription = $customer->getActualSubscription();
            if ($currentSubscription instanceof Subscription) {
                $currentSubscription->cancel();
                if ($currentSubscription->getPaddleId() !== null) {
                    $this->paddleSubscriptionService->cancelSubscription($currentSubscription->getPaddleId());
                }

                $subscriptionData = $this->subscriptionHydrator->toArray($currentSubscription);
                $this->eventBus->fire(new SubscriptionWasCanceledEvent(['subscription' => $subscriptionData]));
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
                    $this->subscriptionHydrator->toArray($subscription),
                'customer' => [
                    'id' => $customer->getId(),
                    'email' => $customer->getEmail(),
                ]
            ];
            $paymentLink = $this->paymentService->getPaymentLink($params);

            $this->eventBus->fire(new SubscriptionWasCreatedEvent($params));
            $result['subscription'] = $params['subscription'] ?? null;
            $result['paymentLink'] = $paymentLink;
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }

    public function unsubscribe(int $subscriptionId, int $customerId): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $subscription = $this->subscriptionRepository->get($subscriptionId);
            if (
                !$subscription instanceof Subscription
                || !$subscription->getCustomer() instanceof Customer
                || $subscription->getCustomer()->getId() !== $customerId
            ) {
                throw new ValidationException('Subscription with this params not found.');
            }

            $subscription->unsubscribe();
            if ($subscription->getPaddleId() !== null) {
                $this->paddleSubscriptionService->cancelSubscription($subscription->getPaddleId());
            } else {
                /**
                 * @todo логировать ошибку
                 * Attempt cancel subscription with empty paddleId.
                 */
            }
            $subscriptionData = $this->subscriptionHydrator->toArray($subscription);

            $this->domainSession->flush();
            $this->eventBus->fire(new SubscriptionWasUnsubscribeEvent(['subscription' => $subscriptionData]));

            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }
}