<?php

namespace Storytale\CustomerActivity\Application\Command\Newsletter\DTO;

class NewsletterSubscriptionDTO
{
    /** @var string|null */
    private ?string $email;

    /** @var int|null */
    private ?int $customerId;

    /** @var string|null */
    private ?string $newsletterType;

    /** @var string|null */
    private ?string $newsletterSubscriptionUuid;

    public function __construct(array $data)
    {
        $this->email = $data['email'] ?? null;
        $this->customerId = $data['customerId'] ?? null;
        $this->newsletterType = $data['newsletterType'] ?? null;
        $this->newsletterSubscriptionUuid = $data['newsletterSubscriptionUuid'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    /**
     * @return string|null
     */
    public function getNewsletterType(): ?string
    {
        return $this->newsletterType;
    }

    /**
     * @return string|null
     */
    public function getNewsletterSubscriptionUuid(): ?string
    {
        return $this->newsletterSubscriptionUuid;
    }
}