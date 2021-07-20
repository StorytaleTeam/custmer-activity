<?php

namespace Storytale\CustomerActivity\Application\Query\Order;

interface OrderDataProvider
{
    /**
     * @param int $customerId
     * @return OrderBasic[]
     */
    public function findListForCustomer(int $customerId): array;

    /**
     * @param int $customerId
     * @param int $orderId
     * @return OrderBasic|null
     */
    public function findOneForCustomer(int  $customerId, int $orderId): ?OrderBasic;
}