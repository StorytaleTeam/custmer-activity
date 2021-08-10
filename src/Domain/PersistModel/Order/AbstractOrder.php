<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

abstract class AbstractOrder extends AbstractEntity
    implements OrderInterface
{
    /** @var int */
    protected int $id;

    /** @var Customer|null */
    protected ?Customer $customer;

    /** @var int */
    protected int $status;

    /** @var OrderPosition[] */
    protected $orderPositions;

    /** @var float */
    protected float $totalPrice;

    /**
     * AbstractOrder constructor.
     * @param Customer $customer
     * @param int $status
     * @param OrderPosition[] $orderPositions
     */
    public function __construct(
        Customer $customer, int $status, array $orderPositions
    )
    {
        foreach ($orderPositions as $orderPosition) {
            $orderPosition->assignOrder($this);
        }

        $this->customer = $customer;
        $this->status = $status;
        $this->orderPositions = $orderPositions;
        $this->totalPrice = 0;
        $this->recalculateTotalPrice();
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return OrderPosition[]
     */
    public function getOrderPositions()
    {
        return $this->orderPositions;
    }

    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function confirm(): void
    {
        $this->status = OrderInterface::STATUS_CONFIRMED;
    }

    public function wasPaid(): void
    {
        $this->status = OrderInterface::STATUS_PAID;
    }

    /**
     * @return float
     */
    public function recalculateTotalPrice(): float
    {
        $totalPrice = 0;
        $this->map(
            function (OrderPosition $op) use (&$totalPrice) {$totalPrice += $op->getProduct()->getTotalPrice();},
            $this->orderPositions
        );
        $this->totalPrice = $totalPrice;

        return $this->totalPrice;
    }
}