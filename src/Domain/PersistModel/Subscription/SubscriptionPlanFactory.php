<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanDTO;

class SubscriptionPlanFactory
{
    public function buildFromDTO(SubscriptionPlanDTO $subscriptionPlanDTO): SubscriptionPlan
    {
        $duration = new Duration($subscriptionPlanDTO->getDurationLabel(), $subscriptionPlanDTO->getDurationCount());

        return new SubscriptionPlan(
            $subscriptionPlanDTO->getName(),
            $subscriptionPlanDTO->getPrice(),
            $duration,
            $subscriptionPlanDTO->getDownloadLimit(),
            SubscriptionPlan::STATUS_DRAFTED
        );
    }
}