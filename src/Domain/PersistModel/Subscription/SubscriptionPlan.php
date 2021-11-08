<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\Contracts\Domain\DomainEventCollection;
use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Product\AbstractProduct;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event\SubscriptionPlanWasCreated;

class SubscriptionPlan extends AbstractProduct
{
    use DomainEventCollection;

    public const STATUS_DRAFTED = 1;
    public const STATUS_PUBLIC = 2;
    public const STATUS_PRIVATE = 3;
    public const STATUS_RENEWAL_ONLY = 4;
    public const STATUS_TRASHED = 5;

    /** @var string|null */
    private ?string $description;

    /** @var TimeRange */
    private TimeRange $duration;

    /** @var TimeRange */
    private TimeRange $chargePeriod;

    /** @var int */
    private int $downloadLimit;

    /** @var array */
    private $subscriptions;

    /** @var int */
    private int $status;

    /** @var int|null */
    private ?int $paddleId;

    /**
     * SubscriptionPlan constructor.
     * @param string $name
     * @param float $price
     * @param string $description
     * @param TimeRange $duration
     * @param TimeRange $chargePeriod
     * @param int $downloadLimit
     * @param int $status
     */
    public function __construct(
        string $name, float $price, string $description,
        TimeRange $duration, TimeRange $chargePeriod,
        int $downloadLimit, int $status
    )
    {
        $this->description = $description;
        $this->price = $price;
        $this->chargePeriod = $chargePeriod;
        $this->duration = $duration;
        $this->downloadLimit = $downloadLimit;
        $this->status = $status;
        $this->paddleId = null;
        parent::__construct($price, $name);
        $this->raiseEvent(new SubscriptionPlanWasCreated($this));
    }

    /**
     * @return TimeRange
     */
    public function getDuration(): TimeRange
    {
        return $this->duration;
    }

    /**
     * @return TimeRange
     */
    public function getChargePeriod(): TimeRange
    {
        return $this->chargePeriod;
    }

    /**
     * @return int
     */
    public function getDownloadLimit(): int
    {
        return $this->downloadLimit;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getPaddleId(): ?int
    {
        return $this->paddleId;
    }

    /**
     * @param int $status
     */
    public function changeStatus(int $status): void
    {
        if ($this->status !== $status) {
            $this->status = $status;
        }
    }

    public function initPaddleId(int $paddleId): void
    {
        if (empty($this->paddleId)) {
            $this->paddleId = $paddleId;
        } else {
            throw new DomainException('Paddle id already isset in SubscriptionPlan id:' . $this->id ?? null);
        }
    }
}