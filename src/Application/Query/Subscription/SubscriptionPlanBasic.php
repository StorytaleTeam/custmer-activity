<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

class SubscriptionPlanBasic implements \JsonSerializable
{
    /** @var int|null */
    private ?int $id;

    /** @var string|null */
    private ?string $name;

    /** @var float|null */
    private ?float $price;

    /** @var int|null */
    private ?int $duration;

    /** @var int|null */
    private ?int $downloadLimit;

    /** @var int|null */
    private ?int $status;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'price' => $this->price ?? null,
            'downloadLimit' => $this->downloadLimit ?? null,
            'status' => $this->status ?? null,
        ];
    }
}