<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event;

use Storytale\Contracts\Domain\AbstractDomainEvent;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

abstract class AbstractSubscriptionPlanDomainEvent extends AbstractDomainEvent
{
    /** @var SubscriptionPlan */
    private SubscriptionPlan $subscriptionPlan;

    public function __construct(SubscriptionPlan $subscriptionPlan)
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    /**
     * @return SubscriptionPlan
     */
    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }
}