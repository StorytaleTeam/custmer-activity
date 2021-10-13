<?php

namespace Storytale\CustomerActivity\Application\Query\Newsletter;

class NewsletterSubscriptionBasic implements \JsonSerializable
{
    /** @var string|null */
    private ?string $email;

    /** @var string|null */
    private ?string $type;

    /** @var string|null */
    private ?string $uuid;

    /** @var bool|null */
    private ?bool $isActive;

    public function jsonSerialize()
    {
        return [
            'email' => $this->email ?? null,
            'type' => $this->type ?? null,
            'uuid' => $this->uuid ?? null,
            'isActive' => $this->isActive ?? null,
        ];
    }
}