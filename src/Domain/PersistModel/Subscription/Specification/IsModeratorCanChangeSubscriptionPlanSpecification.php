<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription\Specification;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\CustomerActivity\Domain\SpecificationInterface;

class IsModeratorCanChangeSubscriptionPlanSpecification implements SpecificationInterface
{
    public function isSatisfiedBy($candidate): bool
    {
        if (!$candidate instanceof SubscriptionPlan) {
            throw new \Exception("Invalid candidate given. Need candidate instance of SubscriptionPlan.");
        }

        if ($candidate->getStatus() === SubscriptionPlan::STATUS_DRAFTED) {
            return false;
        }
        if ($candidate->getStatus() === SubscriptionPlan::STATUS_RENEWAL_ONLY) {
            return false;
        }
        if ($candidate->getStatus() === SubscriptionPlan::STATUS_TRASHED) {
            return false;
        }

        return true;
    }
}