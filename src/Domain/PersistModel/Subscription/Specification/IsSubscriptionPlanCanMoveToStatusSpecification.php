<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription\Specification;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

class IsSubscriptionPlanCanMoveToStatusSpecification
{
    private const SUBSCRIPTION_PLAN_ACTIVE_STATUS = [
        SubscriptionPlan::STATUS_PUBLIC,
        SubscriptionPlan::STATUS_PRIVATE,
        SubscriptionPlan::STATUS_RENEWAL_ONLY,
    ];
    private const SUBSCRIPTION_PLAN_INACTIVE_STATUS = [
        SubscriptionPlan::STATUS_DRAFTED,
        SubscriptionPlan::STATUS_TRASHED
    ];

    public function isSatisfiedBy(SubscriptionPlan $candidate, int $status): bool
    {
        if (in_array($status, self::SUBSCRIPTION_PLAN_INACTIVE_STATUS)) {
            return true;
        }

        if (in_array($status, self::SUBSCRIPTION_PLAN_ACTIVE_STATUS)) {
            if ($candidate->getPaddleId() !== null) {
                return true;
            }
        }

        return false;
    }
}