<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Illustration;

class Illustration
{
    /** @var int */
    private int $id;

    /** @var bool */
    private bool $isFree;

    public function __construct(int $id, bool $isFree)
    {
        $this->id = $id;
        $this->isFree = $isFree;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isFree(): bool
    {
        return $this->isFree;
    }
}