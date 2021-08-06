<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\DomainEvent;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\SharedEvents\Subscription\Membership\MembershipWasActivatedEvent;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\MembershipHydrator;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionHydrator;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\AbstractMembershipDomainEvent;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\MembershipWasActivated;

class MembershipDomainEventHandler
{
    /** @var EventBus */
    private EventBus $eventBus;

    /** @var MembershipHydrator */
    private MembershipHydrator $membershipHydrator;

    /** @var SubscriptionHydrator */
    private SubscriptionHydrator $subscriptionHydrator;

    public function __construct(
        EventBus $eventBus, MembershipHydrator $membershipHydrator,
        SubscriptionHydrator $subscriptionHydrator
    )
    {
        $this->eventBus = $eventBus;
        $this->membershipHydrator = $membershipHydrator;
        $this->subscriptionHydrator = $subscriptionHydrator;
    }

    public function handle(AbstractMembershipDomainEvent $event): void
    {
        switch (true) {
            case $event instanceof MembershipWasActivated:
                $this->handleActivated($event);
                break;
        }
    }

    public function handleActivated(MembershipWasActivated $event): void
    {
        $this->eventBus->fire(new MembershipWasActivatedEvent([
            'membership' => $this->membershipHydrator->toArray($event->getMembership()),
            'subscription' => $this->subscriptionHydrator->toArray($event->getMembership()->getSubscription()),
        ]));
    }
}