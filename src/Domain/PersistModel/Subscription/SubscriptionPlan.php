<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class SubscriptionPlan extends AbstractEntity
{
    public const STATUS_DRAFTED = 1;
    public const STATUS_PUBLIC = 2;
    public const STATUS_PRIVATE = 3;
    public const STATUS_RENEWAL_ONLY = 4;
    public const STATUS_TRASHED = 5;

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
    private $subscriptions;

    /** @var int */
    private int $status;

    /** @var int|null */
    private ?int $paddleId;

    public function __construct(string $name, float $price, Duration $duration, int $downloadLimit, int $status)
    {
        $this->name = $name;
        $this->price = $price;
        $this->duration = $duration;
        $this->downloadLimit = $downloadLimit;
        $this->status = $status;
        $this->paddleId = null;
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