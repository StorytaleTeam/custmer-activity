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
            'id' => $this->id,
            'likesCount' => $this->likesCount,
            'downloadsCount' => $this->downloadsCount,
            'email' => $this->email,
            'name' => $this->name,
        ];
    }

}