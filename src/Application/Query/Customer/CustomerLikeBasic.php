<?php

namespace Storytale\CustomerActivity\Application\Query\Customer;

class CustomerLikeBasic implements \JsonSerializable
{
    /** @var int|null */
    private ?int $id;

    /** @var int|null */
    private ?int $customerId;

    /** @var int|null */
    private ?int $illustrationId;

    /** @var string|null */
    private ?string $createdDate;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id ?? null,
            'customerId' => $this->customerId ?? null,
            'illustrationId' => $this->illustrationId ?? null,
            'createdDate' => $this->createdDate ?? null,
        ];
    }
}