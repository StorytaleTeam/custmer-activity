<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

interface SubscriptionDataProvider
{
    /**
     * @param int $customerId
     * @return SubscriptionBasic[]
     */
    public function findAllByCustomer(int $customerId): array;

    /**
     * @param int $customerId
     * @return SubscriptionBasic|null
     */
    public function findActualForCustomer(int $customerId): ?SubscriptionBasic;
}