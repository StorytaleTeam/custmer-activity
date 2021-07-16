<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

interface OrderRepository
{
    /**
     * @param Order $order
     */
    public function save(Order $order): void;

    /**
     * @param int $id
     * @return Order|null
     */
    public function get(int $id): ?Order;

    /**
     * @param int $orderId
     * @param int $customerId
     * @return Order|null
     */
    public function getByIdAndCustomer(int $orderId, int $customerId): ?Order;
}