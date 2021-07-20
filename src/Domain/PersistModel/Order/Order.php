<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class Order extends AbstractEntity
{
    public const STATUS_NEW = 1;
    public const STATUS_CONFIRMED = 2;
    public const STATUS_PAID = 3;

    /** @var int */
    private int $id;

    /** @var Customer|null */
    private ?Customer $customer;

    /** @var int */
    private int $status;

    /** @var ProductPosition[] */
    private $productPositions;

    /** @var Subscription|null */
    private ?Subscription $subscription;

    public function __construct(Customer $customer, int $status)
    {
        $this->customer = $customer;
        $this->status = $status;
        $this->productPositions = [];
        $this->subscription = null;
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
     * @return Subscription|null
     */
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function addProduct(ProductPosition $productPosition)
    {
        $wasIncremented = false;
        $productPosition->assignOrder($this);
        foreach ($this->productPositions as $addedPosition) {
            if (
                $addedPosition->getProductType() === $productPosition->getProductType()
                && $addedPosition->getProductId() === $productPosition->getProductId()
            ) {
                $addedPosition->addOne();
                $wasIncremented = true;
                break;
            }
        }

        if (!$wasIncremented) {
            $this->productPositions[] = $productPosition;
        }
    }

    /**
     * @return ProductPosition[]
     */
    public function getProductPositions()
    {
        return $this->productPositions;
    }

    public function confirm()
    {
        $this->status = self::STATUS_CONFIRMED;
    }

    public function calculateTotalPrice(): float
    {
        $totalPrice = 0;
        $this->map(
            function (ProductPosition $productPosition) use (&$totalPrice)
            {
                $totalPrice += $productPosition->getPrice()*$productPosition->getCount();
            },
            $this->productPositions
        );

        return $totalPrice;
    }
}