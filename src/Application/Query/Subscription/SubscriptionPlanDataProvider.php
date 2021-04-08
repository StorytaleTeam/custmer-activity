<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

interface SubscriptionPlanDataProvider
{
    /**
     * @param int $id
     * @return SubscriptionPlanBasic|null
     */
    public function find(int $id): ?SubscriptionPlanBasic;

    /**
     * @return SubscriptionPlanBasic[]
     */
    public function findListForAdmin(): array;

    /**
     * @return SubscriptionPlanBasic[]
     */
    public function findListForCustomer(): array;

    /**
     * @param int $id
     * @return SubscriptionPlanBasic|null
     */
    public function findOneForCustomer(int $id): ?SubscriptionPlanBasic;
}