<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class ProductPosition extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var Order|null */
    private ?Order $order;

    /** @var string */
    private string $displayName;

    /** @var string */
    private string $productType;

    /** @var int */
    private int $productId;

    /** @var float */
    private float $price;

    /** @var int */
    private int $count;

    public function __construct(
        string $displayName, string $productType,
        int $productId, float $price, int $count
    )
    {
        $this->order = null;
        $this->displayName = $displayName;
        $this->productType = $productType;
        $this->productId = $productId;
        $this->price = $price;
        $this->count = $count;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getProductType(): string
    {
        return $this->productType;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    public function addOne()
    {
        $this->count++;
    }

    public function assignOrder(Order $order)
    {
        if ($this->order === null) {
            $this->order = $order;
        } else {
            throw new DomainException('Order for ProductPositions already isset');
        }
    }


}