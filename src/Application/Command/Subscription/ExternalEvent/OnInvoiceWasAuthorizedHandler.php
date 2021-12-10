<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\ExternalEvent;

use Storytale\Contracts\Domain\ICompositeDomainEventHandler;
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
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
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

    /** @var ICompositeDomainEventHandler */
    private ICompositeDomainEventHandler $compositeDomainEventHandler;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        SubscriptionProcessingService $subscriptionProcessingService,
        EventBus $eventBus,
        MembershipHydrator $membershipHydrator,
        OrderRepository $orderRepository,
        SubscriptionFactory $subscriptionFactory,
        PaddleSubscriptionService $paddleSubscriptionService,
        ICompositeDomainEventHandler $compositeDomainEventHandler
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
        $this->compositeDomainEventHandler = $compositeDomainEventHandler;
    }

    public function handler(ExternalEvent $event): void
    {
        if ($event instanceof InvoiceWasAuthorizedEvent) {
            $paymentData = $event->getData();
            if (empty($paymentData)) {
                throw new ApplicationException('Get InvoiceWasAuthorizedEvent event with empty data.');
            }

            $oldOrderId = null;
            $orderId = $paymentData['invoice']['orderId'] ?? null;
            if ($orderId === null) {
                $oldOrderId = $paymentData['invoice']['oldOrderId'] ?? null;
                if ($oldOrderId === null) {
                    throw new ApplicationException('Get InvoiceWasAuthorizedEvent with empty orderId & oldOrderId');
                }
            }

            if ($orderId !== null) {
                $order = $this->orderRepository->get($orderId);
            } elseif ($oldOrderId !== null) {
                $order = $this->orderRepository->getByOldId($oldOrderId);
            }
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
            if (!$subscriptionPlan instanceof SubscriptionPlan) {
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
            if (isset($event->getData()['nextBillDate'])) {
                try {
                    $nexBillDate = new \DateTime($event->getData()['nextBillDate']);
                } catch (\Exception $e) {
                    $nexBillDate = null;
                }
                if ($nexBillDate instanceof \DateTime) {
                    $subscription->updateBillDate($nexBillDate);
                }
            }

            $this->subscriptionRepository->save($subscription);
        }

        $invoiceAmount = $event->getData()['invoice']['amount'];
        if ($order->getTotalPrice() <= $invoiceAmount) {
            $order->wasPaid();
            $this->subscriptionProcessingService->wasPaid($subscription, $invoiceAmount);
        }
        $events = $this->domainSession->flush();
        $this->compositeDomainEventHandler->handleArray($events);
        $this->domainSession->close();
    }
}