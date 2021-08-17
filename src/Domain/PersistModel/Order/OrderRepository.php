<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

interface OrderRepository
{
    /**
     * @param AbstractOrder $order
     */
    public function save(AbstractOrder $order): void;

    /**
     * @param int $id
     * @return AbstractOrder|null
     */
    public function get(int $id): ?AbstractOrder;

    /**
     * @param int $oldId
     * @return AbstractOrder|null
     * @deprecated
     */
    public function getByOldId(int $oldId): ?AbstractOrder;

    /**
     * @param int $orderId
     * @param int $customerId
     * @return AbstractOrder|null
     */
    public function getByIdAndCustomer(int $orderId, int $customerId): ?AbstractOrder;
}