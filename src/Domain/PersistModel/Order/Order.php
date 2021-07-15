<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class Order extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var Customer|null */
    private ?Customer $customer;

    /** @var int */
    private int $status;

    /** @var ProductPosition[] */
    private $productPositions;

    public function __construct(Customer $customer, int $status)
    {
        $this->customer = $customer;
        $this->status = $status;
        $this->productPositions = [];
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
}