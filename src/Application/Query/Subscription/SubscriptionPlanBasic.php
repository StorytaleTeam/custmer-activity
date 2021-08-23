<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

class SubscriptionPlanBasic implements \JsonSerializable
{
    /** @var int|null */
    private ?int $id;

    /** @var string|null */
    private ?string $name;

    /** @var string|null */
    private ?string $description;

    /** @var float|null */
    private ?float $price;

    /** @var int|null */
    private ?int $durationCount;

    /** @var string|null */
    private ?string $durationLabel;

    /** @var int|null */
    private ?int $downloadLimit;

    /** @var int|null */
    private ?int $status;

    /** @var string|null */
    private ?string $paddleId;

    /** @var string|null */
    private ?string $createdDate;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'price' => $this->price ?? null,
            'description' => $this->description ?? null,
            'downloadLimit' => $this->downloadLimit ?? null,
            'durationCount' => $this->durationCount ?? null,
            'durationLabel' => $this->durationLabel ?? null,
            'status' => $this->status ?? null,
            'paddleId' => $this->paddleId ?? null,
            'createdDate' => $this->createdDate ?? null,
        ];
    }
}