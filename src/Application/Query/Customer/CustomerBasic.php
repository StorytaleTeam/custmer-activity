<?php

namespace Storytale\CustomerActivity\Application\Query\Customer;

class CustomerBasic implements \JsonSerializable
{
    /** @var int|null */
    private ?int $id;

    /** @var int|null */
    private ?int $likesCount;

    /** @var int|null */
    private ?int $downloadsCount;

    /** @var string|null */
    private ?string $email;

    /** @var string|null */
    private ?string $name;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id ?? null,
            'likesCount' => $this->likesCount ?? null,
            'downloadsCount' => $this->downloadsCount ?? null,
            'email' => $this->email ?? null,
            'name' => $this->name ?? null,
        ];
    }

}