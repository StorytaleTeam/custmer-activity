<?php

namespace Storytale\SkeletonDev\Domain\PersistModel\Order;

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
    public function findById(int $id): ?Order;
}