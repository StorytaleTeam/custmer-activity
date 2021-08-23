<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

class SubscriptionBasic implements \JsonSerializable
{
    /** @var int|null */
    private ?int $id;

    /** @var bool|null */
    private ?bool $autoRenewal;

    /** @var int|null */
    private ?int $planInd;

    /** @var string|null */
    private ?string $name;

    /** @var float|null */
    private ?float $price;

    /** @var int|null */
    private ?int $durationCount;

    /** @var string|null */
    private ?string $durationLabel;

    /** @var int|null */
    private ?int $downloadLimit;

    /** @var int|null */
    private ?int $downloadRemaining;

    /** @var int|null */
    private ?int $status;

    /** @var int|null */
    private ?int $customerId;

    /** @var string|null */
    private ?string $startDate;

    /** @var string|null */
    private ?string $endDate;

    /** @var int|null */
    private ?int $membershipId;

    /** @var int|null */
    private ?int $membershipStatus;

    public function jsonSerialize()
    {
        return [
            'id'            => $this->id ?? null,
            'status'        => $this->status ?? null,
            'autoRenewal'   => $this->autoRenewal ?? null,
            'customer' => [
                'id'                => $this->customerId ?? null,
            ],
            'subscriptionPlan' => [
                'id'                => $this->planId ?? null,
                'name'              => $this->name ?? null,
                'price'             => $this->price ?? null,
                'durationCount'     => $this->durationCount ?? null,
                'durationLabel'     => $this->durationLabel ?? null,
            ],
            'currentMembership' => [
                'id'                => $this->membershipId ?? null,
                'startDate'         => $this->startDate ?? null,
                'endDate'           => $this->endDate ?? null,
                'downloadLimit'     => $this->downloadLimit ?? null,
                'downloadRemaining' => $this->downloadRemaining ?? null,
                'status'            => $this->membershipStatus ?? null
            ],
        ];
    }
}