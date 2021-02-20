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

    /** @var int */
    private int $status;

    /** @var \DateTime */
    private \DateTime $lastActionDate;

    public function __construct(Customer $customer, int $illustrationId, int $status, \DateTime $lastActionDate)
    {
        $this->customer = $customer;
        $this->illustrationId = $illustrationId;
        $this->status = $status;
        $this->lastActionDate = $lastActionDate;
        parent::__construct();
    }
}