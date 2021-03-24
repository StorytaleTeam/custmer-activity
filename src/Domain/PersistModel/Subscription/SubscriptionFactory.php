<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;

class SubscriptionFactory
{
    public function buildFromSubscriptionPlan(SubscriptionPlan $subscriptionPlan, Customer $customer)
    {
        return new Subscription(
            $customer,
            $subscriptionPlan,
            Subscription::STATUS_NEW,
            0,
            true
        );
    }
}