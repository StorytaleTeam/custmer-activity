<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class CustomerLike extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var Customer */
    private Customer $customer;

    /** @var int */
    private int $illustrationId;

    public function __construct(Customer $customer, int $illustrationId, ?\DateTime $createdDate = null)
    {
        $this->customer = $customer;
        $this->illustrationId = $illustrationId;
        parent::__construct($createdDate);
    }

    /**
     * @return int
     */
    public function getIllustrationId(): int
    {
        return $this->illustrationId;
    }
}