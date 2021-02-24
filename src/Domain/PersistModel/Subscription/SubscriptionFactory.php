<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;

class SubscriptionFactory
{
    public function buildFromSubscriptionPlan(SubscriptionPlan $subscriptionPlan, Customer $customer)
    {
        return new Subscription(
            $subscriptionPlan->getName(),
            $subscriptionPlan->getDuration(),
            $subscriptionPlan->getDownloadLimit(),
            $subscriptionPlan->getPrice(),
            $subscriptionPlan->getDownloadLimit(),
            $customer,
            $subscriptionPlan,
            Subscription::STATUS_NEW
        );
    }
}