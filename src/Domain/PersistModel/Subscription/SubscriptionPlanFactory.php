<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanDTO;

class SubscriptionPlanFactory
{
    public function buildFromDTO(SubscriptionPlanDTO $subscriptionPlanDTO): SubscriptionPlan
    {
        return new SubscriptionPlan(
            $subscriptionPlanDTO->getName(),
            $subscriptionPlanDTO->getPrice(),
            $subscriptionPlanDTO->getDuration(),
            $subscriptionPlanDTO->getDownloadLimit(),
            SubscriptionPlan::STATUS_DRAFTED
        );
    }
}