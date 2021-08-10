<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\DomainEvent;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\SharedEvents\Subscription\SubscriptionWasCanceledEvent;
use Storytale\Contracts\SharedEvents\Subscription\SubscriptionWasCreatedEvent;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionHydrator;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\AbstractSubscriptionDomainEvent;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\SubscriptionWasCanceled;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\SubscriptionWasCreated;
use Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Paddle\PaddleSubscriptionService;

class SubscriptionDomainEventHandler
{
    /** @var EventBus */
    private EventBus $eventBus;

    /** @var PaddleSubscriptionService */
    private PaddleSubscriptionService $paddleSubscriptionService;

    /** @var SubscriptionHydrator */
    private SubscriptionHydrator $subscriptionHydrator;

    public function __construct(
        EventBus $eventBus,
        PaddleSubscriptionService $paddleSubscriptionService,
        SubscriptionHydrator $subscriptionHydrator
    )
    {
        $this->eventBus = $eventBus;
        $this->paddleSubscriptionService = $paddleSubscriptionService;
        $this->subscriptionHydrator = $subscriptionHydrator;
    }

    public function handle(AbstractSubscriptionDomainEvent $event): void
    {
        switch(true) {
            case $event instanceof SubscriptionWasCreated:
                $this->handleCreated($event);
                break;
            case $event instanceof SubscriptionWasCanceled:
                $this->handleCanceled($event);
                break;
        }
    }

    public function handleCreated(SubscriptionWasCreated $event): void
    {
        $params = [
            'subscription' =>
                $this->subscriptionHydrator->toArray($event->getSubscription()),
            'customer' => [
                'id' => $event->getSubscription()->getCustomer()->getId(),
                'email' => $event->getSubscription()->getCustomer()->getEmail(),
            ]
        ];

        $this->eventBus->fire(new SubscriptionWasCreatedEvent($params));
    }

    public function handleCanceled(SubscriptionWasCanceled $event): void
    {
        $this->paddleSubscriptionService->cancelSubscription($event->getSubscription()->getPaddleId());
        $subscriptionData = $this->subscriptionHydrator->toArray($event->getSubscription());
        $this->eventBus->fire(new SubscriptionWasCanceledEvent(['subscription' => $subscriptionData]));
    }
}