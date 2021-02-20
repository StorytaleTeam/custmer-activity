<?php

namespace Storytale\CustomerActivity\Application\Query\Customer;

class CustomerDownloadBasic implements \JsonSerializable
{
    /** @var int|null */
    private ?int $id;

    /** @var int|null */
    private ?int $customerId;

    /** @var int|null */
    private ?int $illustrationId;

    /** @var int|null */
    private ?int $reDownloadCount;

    /** @var string|null */
    private ?string $lastDownloadDate;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id ?? null,
            'customerId' => $this->customerId ?? null,
            'illustrationId' => $this->illustrationId ?? null,
            'reDownloadCount' => $this->reDownloadCount ?? null,
            'lastDownloadDate' => $this->lastDownloadDate ?? null,
        ];
    }
}