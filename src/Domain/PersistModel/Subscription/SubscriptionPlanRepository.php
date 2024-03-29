<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

interface SubscriptionPlanRepository
{
    /**
     * @param SubscriptionPlan $subscriptionPlan
     */
    public function save(SubscriptionPlan $subscriptionPlan): void;

    /**
     * @param int $id
     * @return SubscriptionPlan|null
     */
    public function get(int $id): ?SubscriptionPlan;

    /**
     * @param int $oldId
     * @return SubscriptionPlan|null
     * @deprecated
     */
    public function getByOldId(int $oldId): ?SubscriptionPlan;

    /**
     * @return SubscriptionPlan[]
     */
    public function getAll(): array;
}