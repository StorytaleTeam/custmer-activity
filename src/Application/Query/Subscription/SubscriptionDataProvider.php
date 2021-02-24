<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

interface SubscriptionDataProvider
{
    /**
     * @param int $customerId
     * @param int $count
     * @param int $page
     * @return SubscriptionBasic[]
     */
    public function findAllByCustomer(int $customerId, int $count, int $page): array;

    /**
     * @param int $customerId
     * @return SubscriptionBasic|null
     */
    public function findActualForCustomer(int $customerId): ?SubscriptionBasic;

    /**
     * @param int $count
     * @param int $page
     * @param array|null $params
     * @return SubscriptionBasic[]
     */
    public function findList(int $count, int $page, ?array $params = null): array;
}