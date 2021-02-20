<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class Subscription extends AbstractEntity
{
    public const STATUS_NEW                 = 1;
    public const STATUS_WAITING_PAYMENT     = 2;
    public const STATUS_PAID                = 3;
    public const STATUS_ACTIVE              = 4;
    public const STATUS_SPENT               = 5;
    public const STATUS_CANCELED_BY_ADMIN   = 6;
    public const STATUS_CANCELED_BY_CLIENT  = 7;
    public const STATUS_PAUSED              = 8;

    /** @var int */
    private int $id;

    /** @var string */
    private string $name;

    /** @var float */
    private float $price;

    /** @var int */
    private int $duration;

    /** @var int */
    private int $downloadLimit;

    /** @var int */
    private int $downloadRemaining;

    /** @var array */
    private $downloads;

    /** @var int */
    private int $status;

    /** @var Customer */
    private Customer $customer;

    /** @var SubscriptionPlan */
    private SubscriptionPlan $subscriptionPlan;

    /** @var \DateTime|null */
    private ?\DateTime $startDate;

    /** @var \DateTime|null */
    private ?\DateTime $endDate;

    /**
     * Subscription constructor.
     * @param string $name
     * @param int $duration
     * @param int $downloadLimit
     * @param float $price
     * @param int $downloadRemaining
     * @param Customer $customer
     * @param SubscriptionPlan $subscriptionPlan
     * @param int $status
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     */
    public function __construct(
        string $name, int $duration, int $downloadLimit, float $price,
        int $downloadRemaining, Customer $customer, SubscriptionPlan $subscriptionPlan,
        int $status, ?\DateTime $startDate = null, ?\DateTime $endDate = null
    )
    {
        $this->name = $name;
        $this->duration = $duration;
        $this->downloadLimit = $downloadLimit;
        $this->price = $price;
        $this->downloadRemaining = $downloadRemaining;
        $this->customer = $customer;
        $this->subscriptionPlan = $subscriptionPlan;
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        parent::__construct();
    }
}