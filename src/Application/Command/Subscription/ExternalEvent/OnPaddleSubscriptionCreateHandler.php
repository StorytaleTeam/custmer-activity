<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\ExternalEvent;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\EventBus\ExternalEvent;
use Storytale\Contracts\EventBus\ExternalEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Payment\Paddle\GeneralizedPaddleEvent;
use Storytale\Contracts\SharedEvents\Subscription\SubscriptionWasCreatedEvent;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionDTOAssembler;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Order\Order;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPositionsService;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;

class OnPaddleSubscriptionCreateHandler implements ExternalEventHandler
{
    private const EVENT_NAME_PADDLE_CREATE_SUBSCRIPTION = 'subscription_created';

    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var SubscriptionFactory */
    private SubscriptionFactory $subscriptionFactory;

    /** @var OrderRepository */
    private OrderRepository $orderRepository;

    /** @var ProductPositionsService */
    private ProductPositionsService $productPositionService;

    /** @var SubscriptionDTOAssembler */
    private SubscriptionDTOAssembler $subscriptionDTOAssembler;

    /** @var EventBus */
    private EventBus $eventBus;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        SubscriptionFactory $subscriptionFactory,
        OrderRepository $orderRepository,
        ProductPositionsService $productPositionService,
        SubscriptionDTOAssembler $subscriptionDTOAssembler,
        EventBus $eventBus
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->orderRepository = $orderRepository;
        $this->productPositionService = $productPositionService;
        $this->subscriptionDTOAssembler = $subscriptionDTOAssembler;
        $this->eventBus = $eventBus;
    }

    public function handler(ExternalEvent $event): void
    {
        if ($event instanceof GeneralizedPaddleEvent) {
            $alertName = $event->getPaddleData()['alert_name'] ?? null;
            if ($alertName === self::EVENT_NAME_PADDLE_CREATE_SUBSCRIPTION) {
                $orderId = $event->getStorytaleData()['orderId'] ?? null;
                if ($orderId === null) {
                    throw new ApplicationException('Get OnPaddleSubscriptionCreateHandler with empty orderId');
                }
                $order = $this->orderRepository->get($orderId);
                if (!$order instanceof Order) {
                    throw new ApplicationException("Order with id $orderId not found.");
                }
                if (!$order->getCustomer() instanceof Customer) {
                    throw new ApplicationException('Not found customer for order '. $order->getId());
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

                $paddleSubscriptionId = $event->getPaddleData()['subscription_id'] ?? null;
                $subscription->initPaddleId($paddleSubscriptionId);
                $this->domainSession->flush();

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
            }
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