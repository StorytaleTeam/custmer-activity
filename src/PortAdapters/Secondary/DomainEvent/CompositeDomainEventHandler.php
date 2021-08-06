<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\DomainEvent;

use Storytale\Contracts\Domain\AbstractDomainEvent;
use Storytale\Contracts\Domain\ICompositeDomainEventHandler;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\AbstractMembershipDomainEvent;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\AbstractSubscriptionDomainEvent;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\AbstractSubscriptionPlanDomainEvent;
use Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\DomainEvent\MembershipDomainEventHandler;
use Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\DomainEvent\SubscriptionDomainEventHandler;
use Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\DomainEvent\SubscriptionPlanDomainEventHandler;

class CompositeDomainEventHandler implements ICompositeDomainEventHandler
{
    /** @var SubscriptionPlanDomainEventHandler */
    private SubscriptionPlanDomainEventHandler $subscriptionPlanHandler;

    /** @var MembershipDomainEventHandler */
    private MembershipDomainEventHandler $membershipHandler;

    /** @var SubscriptionDomainEventHandler */
    private SubscriptionDomainEventHandler $subscriptionHandler;

    public function __construct(
        SubscriptionPlanDomainEventHandler $subscriptionPlanHandler,
        MembershipDomainEventHandler $membershipHandler,
        SubscriptionDomainEventHandler $subscriptionHandler
    )
    {
        $this->subscriptionPlanHandler = $subscriptionPlanHandler;
        $this->membershipHandler = $membershipHandler;
        $this->subscriptionHandler = $subscriptionHandler;
    }

    public function handle(AbstractDomainEvent $event): void
    {
        switch (true) {
            case $event instanceof AbstractSubscriptionPlanDomainEvent:
                $this->subscriptionPlanHandler->handle($event);
                break;
            case $event instanceof AbstractMembershipDomainEvent:
                $this->membershipHandler->handle($event);
                break;
            case $event instanceof AbstractSubscriptionDomainEvent:
                $this->subscriptionHandler->handle($event);
                break;
        }
    }

    public function handleArray(iterable $events): void
    {
        foreach ($events AS $event) {
            if(!$event instanceof AbstractDomainEvent) {
                throw new ApplicationException('Invalid event type provided.');
            }
            $this->handle($event);
        }
    }
}