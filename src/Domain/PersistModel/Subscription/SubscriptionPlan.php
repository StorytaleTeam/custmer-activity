<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

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

    /** @var int */
    private int $duration;

    /** @var int */
    private int $downloadLimit;

    /** @var array */
    private $subscriptions;

    /** @var int */
    private int $status;

    public function __construct(string $name, float $price, int $duration, int $downloadLimit, int $status)
    {
        $this->name = $name;
        $this->price = $price;
        $this->duration = $duration;
        $this->downloadLimit = $downloadLimit;
        $this->status = $status;
        parent::__construct();
    }
}