<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

interface SubscriptionDataProvider
{
    /**
     * @param int $subscriptionId
     * @param int $customerId
     * @return SubscriptionBasic|null
     */
    public function findOneForCustomer(int $subscriptionId, int $customerId): ?SubscriptionBasic;

    /**
     * @param int $customerId
     * @param int $count
     * @param int $page
     * @return SubscriptionBasic[]
     */
    public function findAllByCustomer(int $customerId, int $count, int $page): array;

    /**
     * @param int $count
     * @param int $page
     * @param array|null $params
     * @return SubscriptionBasic[]
     */
    public function findList(int $count, int $page, ?array $params = null): array;
}