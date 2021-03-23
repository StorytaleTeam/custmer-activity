<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

class Duration
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