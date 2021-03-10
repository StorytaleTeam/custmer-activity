<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

class Duration
{
    /** @var string */
    private string $label;

    /** @var int */
    private int $count;

    public function __construct(string $label, int $count)
    {
        $this->label = $label;
        $this->count = $count;
    }
}