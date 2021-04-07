<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerDownload;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class Membership extends AbstractEntity
{
    public const STATUS_NEW                 = 1;
    public const STATUS_PAID                = 2;
    public const STATUS_ACTIVE              = 3;
    public const STATUS_SPENT_LIMIT         = 4;
    public const STATUS_DURATION_EXPIRED    = 5;
    public const STATUS_CANCELED_BY_ADMIN   = 6;
    public const STATUS_PAUSED              = 7;

    /** @var int */
    private int $id;

    /** @var Subscription */
    private Subscription $subscription;

    /** @var float */
    private float $amountReceived;

    /** @var int  */
    private int $status;

    /** @var int */
    private int $downloadLimit;

    /** @var array */
    private $downloads;

    /** @var \DateTime|null */
    private ?\DateTime $startDate;

    /** @var \DateTime|null */
    private ?\DateTime $endDate;

    /** @var int|null */
    private ?int $cycleNumber;

    public function __construct(
        Subscription $subscription, float $amountReceived,
        int $status, int $downloadLimit, ?int $cycleNumber = null
    )
    {
        parent::__construct();
        $this->subscription = $subscription;
        $this->amountReceived = $amountReceived;
        $this->status = $status;
        $this->downloadLimit = $downloadLimit;
        $this->cycleNumber = $cycleNumber;
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
    public function getAmountReceived(): float
    {
        return $this->amountReceived;
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
    public function getDownloadLimit(): int
    {
        return $this->downloadLimit;
    }

    /**
     * @return array
     */
    public function getDownloads(): array
    {
        return $this->downloads;
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
     * @return int
     */
    public function getDownloadRemaining(): int
    {
        return $this->downloadLimit - count($this->downloads);
    }

    /**
     * @return int|null
     */
    public function getCycleNumber(): ?int
    {
        return $this->cycleNumber;
    }

    /**
     * @param int $cycleNumber
     * @throws DomainException
     */
    public function initCycleNumber(int $cycleNumber): void
    {
        if ($this->cycleNumber === null) {
            $this->cycleNumber = $cycleNumber;
        } else throw new DomainException('CycleNumber already isset in membership ' . $this->id);
    }

    public function newDownload(CustomerDownload $customerDownload): void
    {
        if ($this->getDownloadRemaining() < 1) {
            throw new DomainException('Download limit reached.');
        }
        if ($customerDownload->getMembership() !== null) {
            throw new DomainException('Download already assign to membership.');
        }
        $customerDownload->setMembership($this);
        $this->downloads[] = $customerDownload;

        if ($this->getDownloadRemaining() < 1) {
            $this->status = self::STATUS_SPENT_LIMIT;
        }
    }

    public function activate(int $cycleNumber)
    {
        if ($this->status == self::STATUS_PAID) {
            $this->status = self::STATUS_ACTIVE;
            $this->startDate = new \DateTime();
            $durationCount = $this->subscription->getSubscriptionPlan()->getDuration()->getCount();
            $durationLabel = $this->subscription->getSubscriptionPlan()->getDuration()->getLabel();
            $this->endDate = (new \DateTime())->modify("+$durationCount $durationLabel");
            $this->cycleNumber = $cycleNumber;
        } else {
            throw new DomainException('Attempt to reactivate the Membership ' . $this->id);
        }
    }

    public function paid(): void
    {
        if ($this->status !== self::STATUS_NEW) {
            /** @todo need alert to manager */
            throw new DomainException('Retrying payment for membership ' . $this->id);
        }
        $this->status = self::STATUS_PAID;
    }

    public function expire(): void
    {
        $this->status = self::STATUS_DURATION_EXPIRED;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }
}