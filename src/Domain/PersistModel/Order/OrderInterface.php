<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;

interface OrderInterface
{
    public const STATUS_NEW = 1;
    public const STATUS_CONFIRMED = 2;
    public const STATUS_PAID = 3;
    public const STATUS_CANCELED = 3;


    public function confirm(): void;
    public function wasPaid(): void;

    /**
     * @return float
     */
    public function getTotalPrice(): float;

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param $product
     * @throws DomainException
     */
    public function addProduct($product): void;

    /**
     * @return OrderPosition[]
     */
    public function getOrderPositions();
}