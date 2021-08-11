<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

/**
 * Class SubscriptionPlanHydrator
 * @package Storytale\CustomerActivity\Application\Command\Subscription\DTO
 */
class SubscriptionPlanHydrator
{
    public function toArray(SubscriptionPlan $subscriptionPlan): array
    {
        return [
            'id' => $subscriptionPlan->getId(),
            'paddleId' => $subscriptionPlan->getPaddleId() ?? null,
            'name' => $subscriptionPlan->getName(),
            'price' => $subscriptionPlan->getPrice(),
            'duration' => [
                'count' => $subscriptionPlan->getDuration()->getCount(),
                'label' => $subscriptionPlan->getDuration()->getLabel(),
            ],
        ];
    }
}