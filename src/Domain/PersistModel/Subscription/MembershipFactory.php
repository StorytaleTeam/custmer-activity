<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

class MembershipFactory
{
    public function build(Subscription $subscription, float $amountReceived)
    {
        return new Membership(
            $subscription,
            $amountReceived,
            Membership::STATUS_NEW,
            $subscription->getSubscriptionPlan()->getDownloadLimit(),
            null
        );
    }
}