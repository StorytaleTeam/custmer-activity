<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event;

use Storytale\Contracts\Domain\AbstractDomainEvent;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;

abstract class AbstractSubscriptionDomainEvent extends AbstractDomainEvent
{
    /** @var Subscription */
    protected Subscription $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }
}