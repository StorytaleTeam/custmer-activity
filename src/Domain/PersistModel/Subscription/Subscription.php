<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerDownload;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class Subscription extends AbstractEntity
{
    public const STATUS_NEW                 = 1;
    public const STATUS_WAITING_PAYMENT     = 2;
    public const STATUS_PAID                = 3;
    public const STATUS_ACTIVE              = 4;
    public const STATUS_SPENT_LIMIT         = 5;
    public const STATUS_DURATION_EXPIRED    = 6;
    public const STATUS_CANCELED_BY_ADMIN   = 7;
    public const STATUS_PAUSED              = 8;

    /** @var int */
    private int $id;

    /** @var string */
    private string $name;

    /** @var float */
    private float $price;

    /** @var Duration */
    private Duration $duration;

    /** @var int */
    private int $downloadLimit;

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
     * @param Duration $duration
     * @param int $downloadLimit
     * @param float $price
     * @param Customer $customer
     * @param SubscriptionPlan $subscriptionPlan
     * @param int $status
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     */
    public function __construct(
        string $name, Duration $duration, int $downloadLimit, float $price,
        Customer $customer, SubscriptionPlan $subscriptionPlan,
        int $status, ?\DateTime $startDate = null, ?\DateTime $endDate = null
    )
    {
        $this->name = $name;
        $this->duration = $duration;
        $this->downloadLimit = $downloadLimit;
        $this->price = $price;
        $this->customer = $customer;
        $this->subscriptionPlan = $subscriptionPlan;
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getDownloadRemaining(): int
    {
        return $this->downloadLimit - count($this->downloads);
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param CustomerDownload $customerDownload
     * @throws DomainException
     */
    public function newDownload(CustomerDownload $customerDownload): void
    {
        if ($this->getDownloadRemaining() < 1) {
            throw new DomainException('Download limit reached.');
        }
        if ($customerDownload->getSubscription() !== null) {
            throw new DomainException('Download already assign to subscription.');
        }
        $customerDownload->setSubscription($this);

        $this->downloads[] = $customerDownload;
    }

    /**
     * @return SubscriptionPlan
     */
    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function changeStatus(int $status): void
    {
        if ($this->status !== $status) {
            $this->status = $status;
        }
    }

    /**
     * @throws DomainException
     */
    public function activate(): void
    {
        if ($this->customer->getActualSubscription() instanceof Subscription) {
            throw new DomainException('Subscription ' . $this->id
                . ' activation is not possible. User already has an active subscription');
        }

        if (!in_array($this->status, [self::STATUS_NEW, self::STATUS_WAITING_PAYMENT])) {
            throw new DomainException('Reactivation of the subscription '
            . $this->id .'is not possible');
        }

        $this->status = self::STATUS_ACTIVE;

        $this->startDate = new \DateTime();
        $this->endDate = (new \DateTime())->modify('+' . $this->duration->getCount() . ' ' . $this->duration->getLabel());
    }
}