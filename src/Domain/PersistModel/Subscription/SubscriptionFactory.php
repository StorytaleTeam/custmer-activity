<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;

class SubscriptionFactory
{
    public function buildFromSubscriptionPlan(
        SubscriptionPlan $subscriptionPlan,
        Customer $customer
    ): Subscription
    {
        return new Subscription(
            $customer,
            $subscriptionPlan,
            Subscription::STATUS_NEW,
            0,
            true
        );
    }

    public function buildAllFields(
        Customer $customer,
        SubscriptionPlan $subscriptionPlan,
        int $status,
        int $membershipCycle,
        bool $autoRenewal,
        ?string $paddleId = null,
        ?int $oldId = null,
        ?\DateTime $createdDate = null
    ): ?Subscription
    {
        return new Subscription(
            $customer,
            $subscriptionPlan,
            $status,
            $membershipCycle,
            $autoRenewal,
            $paddleId,
            $oldId,
            $createdDate
        );
    }
}