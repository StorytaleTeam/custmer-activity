<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Product;

use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

abstract class AbstractProduct extends AbstractEntity
    implements ProductInterface
{
    /** @var int */
    protected int $id;

    /** @var float */
    protected float $price;

    /** @var float */
    protected float $totalPrice;

    public function __construct(float $price, float $totalPrice)
    {
        $this->price = $price;
        $this->totalPrice = $totalPrice;
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
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }
}