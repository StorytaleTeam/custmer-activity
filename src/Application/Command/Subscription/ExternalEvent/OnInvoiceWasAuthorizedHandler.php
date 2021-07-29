<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\ExternalEvent;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\EventBus\ExternalEvent;
use Storytale\Contracts\EventBus\ExternalEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Payment\InvoiceWasAuthorizedEvent;
use Storytale\Contracts\SharedEvents\Subscription\Membership\MembershipWasActivatedEvent;
use Storytale\Contracts\SharedEvents\Subscription\SubscriptionWasCreatedEvent;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\MembershipDTOAssembler;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionDTOAssembler;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Order\Order;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPositionsService;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionProcessingService;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;
use Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Paddle\PaddleSubscriptionService;

class OnInvoiceWasAuthorizedHandler implements ExternalEventHandler
{
    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var SubscriptionProcessingService */
    private SubscriptionProcessingService $subscriptionProcessingService;

    /** @var EventBus */
    private EventBus $eventBus;

    /** @var MembershipDTOAssembler */
    private MembershipDTOAssembler $membershipDTOAssembler;

    /** @var SubscriptionDTOAssembler */
    private SubscriptionDTOAssembler $subscriptionDTOAssembler;

    /** @var OrderRepository */
    private OrderRepository $orderRepository;

    /** @var SubscriptionFactory */
    private SubscriptionFactory $subscriptionFactory;

    /** @var ProductPositionsService */
    private ProductPositionsService $productPositionService;

    /** @var PaddleSubscriptionService */
    private PaddleSubscriptionService $paddleSubscriptionService;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        SubscriptionProcessingService $subscriptionProcessingService,
        EventBus $eventBus,
        MembershipDTOAssembler $membershipDTOAssembler,
        SubscriptionDTOAssembler $subscriptionDTOAssembler,
        OrderRepository $orderRepository,
        SubscriptionFactory $subscriptionFactory,
        ProductPositionsService $productPositionService,
        PaddleSubscriptionService $paddleSubscriptionService
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionProcessingService = $subscriptionProcessingService;
        $this->eventBus = $eventBus;
        $this->membershipDTOAssembler = $membershipDTOAssembler;
        $this->subscriptionDTOAssembler = $subscriptionDTOAssembler;
        $this->orderRepository = $orderRepository;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->productPositionService = $productPositionService;
        $this->paddleSubscriptionService = $paddleSubscriptionService;
    }

    public function handler(ExternalEvent $event): void
    {
        if ($event instanceof InvoiceWasAuthorizedEvent) {
            $paymentData = $event->getData();
            if (empty($paymentData)) {
                throw new ApplicationException('Get InvoiceWasAuthorizedEvent event with empty data.');
            }
            $orderId = $paymentData['invoice']['orderId'] ?? null;
            if ($orderId === null) {
                throw new ApplicationException('Get InvoiceWasAuthorizedEvent with empty orderId');
            }

            $order = $this->orderRepository->get($orderId);
            if (!$order instanceof Order) {
                throw new ApplicationException("Order with id $orderId not found.");
            }
            if (!$order->getCustomer() instanceof Customer) {
                throw new ApplicationException('Not found customer for order '. $order->getId());
            }

            $oldCustomerSubscription = $order->getCustomer()->getActualSubscription();

            $subscriptionPlan = null;
            foreach ($order->getProductPositions() as $productPosition) {
                $product = $this->productPositionService->getProductByProductPosition($productPosition);
                if ($product instanceof SubscriptionPlan) {
                    $subscriptionPlan = $product;
                    break;
                }
            }
            if (!$subscriptionPlan instanceof SubscriptionPlan) {
                throw new ApplicationException('Not found subscriptionPlan for order ' . $order->getId());
            }

            $subscriptionWasCreated = false;
            $subscription = $order->getSubscription();
            if (!$subscription instanceof Subscription) {
                $subscriptionPlan = $this->getSubscriptionPlanFromOrder($order);
                $subscription = $this->subscriptionFactory
                    ->buildFromSubscriptionPlan($subscriptionPlan, $order->getCustomer());
                $order->assignSubscription($subscription);
                $this->subscriptionRepository->save($subscription);
                $subscriptionWasCreated = true;
            }

            $oldMembership = $subscription->getCurrentMembership();
            $oldMembershipId = $oldMembership instanceof Membership ? $oldMembership->getId() : null;

            if ($order->getTotalPrice() == $paymentData['invoice']['amount']) {
                $order->wasPaid();
                $this->subscriptionProcessingService->wasPaid($subscription, $paymentData['invoice']['amount']);
            }
            $this->domainSession->flush();

            /**
             * @Annotation cancel subscription on paddle
             * @todo переделать до доменных ивентах
             */
            if (
                $oldCustomerSubscription instanceof Subscription
                && ($oldCustomerSubscription->getId() !== $subscription->getId())
            ) {
                if ($oldCustomerSubscription->getPaddleId() !== null) {
                    $this->paddleSubscriptionService
                        ->cancelSubscription($oldCustomerSubscription->getPaddleId());
                }
            }

            $newMembership = $subscription->getCurrentMembership();
            if (
                $newMembership instanceof Membership
                && $newMembership->getId() !== $oldMembershipId
                && $newMembership->getStatus() === Membership::STATUS_ACTIVE
            ) {
                $this->eventBus->fire(new MembershipWasActivatedEvent([
                    'membership' => $this->membershipDTOAssembler->toArray($newMembership),
                    'subscription' => $this->subscriptionDTOAssembler->toArray($subscription),
                ]));
            }

            if ($subscriptionWasCreated === true) {
                $params = [
                    'subscription' =>
                        $this->subscriptionDTOAssembler->toArray($subscription),
                    'customer' => [
                        'id' => $subscription->getCustomer()->getId(),
                        'email' => $subscription->getCustomer()->getEmail(),
                    ]
                ];
                $this->eventBus->fire(new SubscriptionWasCreatedEvent($params));
            }
        } else {
            throw new ApplicationException('Invalid event type provided.');
        }
    }


    /**
     * @param Order $order
     * @return SubscriptionPlan
     * @throws ApplicationException
     */
    private function getSubscriptionPlanFromOrder(Order $order): SubscriptionPlan
    {
        $subscriptionPlan = null;
        foreach ($order->getProductPositions() as $productPosition) {
            $product = $this->productPositionService->getProductByProductPosition($productPosition);
            if ($product instanceof SubscriptionPlan) {
                $subscriptionPlan = $product;
                break;
            }
        }
        if (!$subscriptionPlan instanceof SubscriptionPlan) {
            throw new ApplicationException('Not found subscriptionPlan for order ' . $order->getId());
        }

        return $subscriptionPlan;
    }
}