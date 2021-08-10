<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Product\ProductInterface;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class OrderPosition extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var ProductInterface  */
    private ProductInterface $product;

    /** @var string */
    private string $displayName;

    /** @var OrderInterface|null */
    private ?OrderInterface $order;

    public function __construct(ProductInterface $product, string $displayName)
    {
        $this->product = $product;
        $this->displayName = $displayName;
        $this->order = null;
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
     * @return ProductInterface
     */
    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param OrderInterface $order
     * @throws DomainException
     */
    public function assignOrder(OrderInterface $order): void
    {
        if (!$this->order instanceof OrderInterface) {
            $this->order = $order;
        } else {
            throw new DomainException('Order already exist.');
        }
    }
}