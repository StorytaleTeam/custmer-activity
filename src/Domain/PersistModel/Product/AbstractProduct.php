<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Product;

use Storytale\CustomerActivity\Domain\DomainException;
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

    /**
     * @var int|null
     * @deprecated
     */
    protected ?int $oldId;

    public function __construct(float $price, float $totalPrice)
    {
        $this->price = $price;
        $this->totalPrice = $totalPrice;
        $this->oldId = null;
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

    /**
     * @param int $oldId
     * @throws DomainException
     * @deprecated
     */
    public function initOldId(int $oldId): void
    {
        if ($this->oldId === null) {
            $this->oldId = $oldId;
        } else {
            throw new DomainException('OldId already isset.');
        }
    }

    /**
     * @return int|null
     * @deprecated
     */
    public function getOldId(): ?int
    {
        return $this->oldId;
    }
}