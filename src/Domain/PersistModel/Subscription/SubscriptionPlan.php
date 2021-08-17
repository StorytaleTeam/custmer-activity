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

    /** @var Duration */
    private Duration $duration;

    /** @var int */
    private int $downloadLimit;

    /** @var array */
    private $subscriptions;

    /** @var int */
    private int $status;

    /** @var int|null */
    private ?int $paddleId;

    public function __construct(string $name, float $price, Duration $duration, int $downloadLimit, int $status)
    {
        $this->price = $price;
        $this->duration = $duration;
        $this->downloadLimit = $downloadLimit;
        $this->status = $status;
        $this->paddleId = null;
        parent::__construct($price, $name);
        $this->raiseEvent(new SubscriptionPlanWasCreated($this));
    }

    /**
     * @return Duration
     */
    public function getDuration(): Duration
    {
        return $this->duration;
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

    /**
     * @return int|null
     */
    public function getPaddleId(): ?int
    {
        return $this->paddleId;
    }
}