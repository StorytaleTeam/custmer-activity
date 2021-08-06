<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\ExternalEvent;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\EventBus\ExternalEvent;
use Storytale\Contracts\EventBus\ExternalEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Payment\InvoiceWasAuthorizedEvent;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\MembershipHydrator;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Order\AbstractOrder;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionFactory;
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

    /** @var MembershipHydrator */
    private MembershipHydrator $membershipHydrator;

    /** @var OrderRepository */
    private OrderRepository $orderRepository;

    /** @var SubscriptionFactory */
    private SubscriptionFactory $subscriptionFactory;

    /** @var PaddleSubscriptionService */
    private PaddleSubscriptionService $paddleSubscriptionService;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        SubscriptionProcessingService $subscriptionProcessingService,
        EventBus $eventBus,
        MembershipHydrator $membershipHydrator,
        OrderRepository $orderRepository,
        SubscriptionFactory $subscriptionFactory,
        PaddleSubscriptionService $paddleSubscriptionService
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionProcessingService = $subscriptionProcessingService;
        $this->eventBus = $eventBus;
        $this->membershipHydrator = $membershipHydrator;
        $this->orderRepository = $orderRepository;
        $this->subscriptionFactory = $subscriptionFactory;
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
            if (!$order instanceof AbstractOrder) {
                throw new ApplicationException("Order with id $orderId not found.");
            }
            if (!$order->getCustomer() instanceof Customer) {
                throw new ApplicationException('Not found customer for order ' . $order->getId());
            }

            if ($order instanceof OrderSubscription) {
                $this->handlerForOrderSubscription($order, $event);
            } else {
                throw new ApplicationException('Unsupported order type given.');
            }

        }
    }

    private function handlerForOrderSubscription(OrderSubscription $order, InvoiceWasAuthorizedEvent $event): void
    {
        $subscription = $order->getSubscription();
        if (!$subscription instanceof Subscription) {
            $subscriptionPlan = $order->getSubscriptionPlan();
            if (!$subscriptionPlan instanceof Subscription) {
                throw new ApplicationException('Get order with empty subscriptionPlan');
            }
            $subscription = $this->subscriptionFactory
                ->buildFromSubscriptionPlan($subscriptionPlan, $order->getCustomer());
            $order->assignSubscription($subscription);

            $paddleSubscriptionId = $event->getData()['paddle']['subscription_id'] ?? null;
            if ($paddleSubscriptionId === null) {
                throw new ApplicationException('Get InvoiceWasAuthorizedEvent with empty paddle_subscription_id');
            }
            $subscription->initPaddleId($paddleSubscriptionId);
            $this->subscriptionRepository->save($subscription);
        }

        $oldMembership = $subscription->getCurrentMembership();
        $oldMembershipId = $oldMembership instanceof Membership ? $oldMembership->getId() : null;

        $invoiceAmount = $event->getData()['invoice']['amount'];
        if ($order->getTotalPrice() == $invoiceAmount) {
            $order->wasPaid();
            $this->subscriptionProcessingService->wasPaid($subscription, $invoiceAmount);
        }
        $events = $this->domainSession->flush();
    }
}