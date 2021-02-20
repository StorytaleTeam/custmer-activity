<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

interface SubscriptionPlanDataProvider
{
    /**
     * @return SubscriptionPlanBasic[]
     */
    public function findListForAdmin(): array;

    /**
     * @return SubscriptionPlanBasic[]
     */
    public function findListForCustomer(): array;
}