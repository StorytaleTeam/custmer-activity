<?php

namespace Storytale\SkeletonDev\Domain\PersistModel\Order;

class Order
{
    /** @var int */
    private int $id;

    /** @var int */
    private int $customerId;

    /** @var int */
    private int $productId;

    /** @var \DateTime */
    private \DateTime $createdDate;

    /** @var \DateTime|null */
    private ?\DateTime $soldDate;

    public function __construct(
        int $customerId, int $productId, \DateTime $createdDate
    )
    {
        $this->id = 1;
        $this->customerId = $customerId;
        $this->productId = $productId;
        $this->createdDate = $createdDate;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function wasSold()
    {
        if ($this->soldDate === null) {
            $this->soldDate = new \DateTime();
        }
    }
}