<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class CustomerDownload extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var Customer */
    private Customer $customer;

    /** @var Subscription */
    private Subscription $subscription;

    /** @var int */
    private int $illustrationId;

    /** @var int */
    private int $reDownloadCount;

    /** @var \DateTime */
    private \DateTime $lastDownloadDate;

    public function __construct(Customer $customer, Subscription $subscription, int $illustrationId, int $reDownloadCount, \DateTime $lastDownloadDate)
    {
        $this->customer = $customer;
        $this->subscription = $subscription;
        $this->illustrationId = $illustrationId;
        $this->reDownloadCount = $reDownloadCount;
        $this->lastDownloadDate = $lastDownloadDate;
        parent::__construct();
    }
}