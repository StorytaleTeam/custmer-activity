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

    /** @var string */
    protected string $name;

    /**
     * @var int|null
     * @deprecated
     */
    protected ?int $oldId;

    public function __construct(float $price, string $name)
    {
        $this->price = $price;
        $this->name = $name;
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

    public function getProductName(): string
    {
        return $this->name;
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