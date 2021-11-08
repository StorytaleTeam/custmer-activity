<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanDTO;
use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Specification\IsValidDurationForSubscriptionPlanSpecification;

class SubscriptionPlanFactory
{
    public function buildFromDTO(SubscriptionPlanDTO $subscriptionPlanDTO): SubscriptionPlan
    {
        $duration = new TimeRange($subscriptionPlanDTO->getDurationLabel(), $subscriptionPlanDTO->getDurationCount());
        $chargePeriod = new TimeRange($subscriptionPlanDTO->getChargePeriodLabel(), $subscriptionPlanDTO->getChargePeriodCount());
        $durationSpecification = new IsValidDurationForSubscriptionPlanSpecification();
        if ($durationSpecification->isSatisfiedBy($duration, $chargePeriod) !== true) {
            throw new DomainException('Error while create SubscriptionPlan. ' . $durationSpecification->getMessages());
        }

        return new SubscriptionPlan(
            $subscriptionPlanDTO->getName(),
            $subscriptionPlanDTO->getPrice(),
            $subscriptionPlanDTO->getDescription(),
            $duration,
            $chargePeriod,
            $subscriptionPlanDTO->getDownloadLimit(),
            SubscriptionPlan::STATUS_DRAFTED
        );
    }
}