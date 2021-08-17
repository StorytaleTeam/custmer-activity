<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

class MembershipFactory
{
    public function build(Subscription $subscription, float $amountReceived): Membership
    {
        return new Membership(
            $subscription,
            $amountReceived,
            Membership::STATUS_NEW,
            $subscription->getSubscriptionPlan()->getDownloadLimit(),
            null
        );
    }

    public function buildAllFields(
        Subscription $subscription,
        float $amountReceived,
        int $status,
        int $downloadLimit,
        ?int $cycleNumber = null,
        ?int $oldId = null,
        ?\DateTime $createdDate = null
    ): Membership
    {
        return new Membership(
            $subscription,
            $amountReceived,
            $status,
            $downloadLimit,
            $cycleNumber,
            $oldId,
            $createdDate
        );
    }
}