<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\DomainEvent;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\AbstractSubscriptionPlanDomainEvent;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\SubscriptionPlanWasCreated;
use Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Paddle\PaddleSubscriptionService;

class SubscriptionPlanDomainEventHandler
{
    /** @var PaddleSubscriptionService */
    private PaddleSubscriptionService $paddleSubscriptionService;

    public function __construct(PaddleSubscriptionService $paddleSubscriptionService)
    {
        $this->paddleSubscriptionService = $paddleSubscriptionService;
    }

    public function handle(AbstractSubscriptionPlanDomainEvent $event): void
    {
        switch (true) {
            case $event instanceof SubscriptionPlanWasCreated:
                $this->handleCreated($event);
                break;
        }
    }

    public function handleCreated(SubscriptionPlanWasCreated $event): void
    {
        if (empty($event->getSubscriptionPlan()->getPaddleId())) {
            $this->paddleSubscriptionService->createSubscriptionPlan($event->getSubscriptionPlan());
        }
    }
}