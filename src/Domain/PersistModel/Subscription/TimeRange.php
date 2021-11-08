<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

/**
 * Class TimeRange
 * @package Storytale\CustomerActivity\Domain\PersistModel\Subscription
 * @todo make not null in mapping
 */
class TimeRange
{
    public const AVAILABLE_LABEL = ['day', 'month'];

    /** @var string */
    private string $label;

    /** @var int */
    private int $count;

    public function __construct(string $label, int $count)
    {
        $this->label = $label;
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }
}