<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

class SubscriptionSigningDTO
{
    /** @var int|null */
    private ?int $customerId;

    /** @var int|null */
    private ?int $subscriptionPlanId;

    public function __construct(array $data)
    {
        $this->customerId = $data['customerId'] ?? null;
        $this->subscriptionPlanId = $data['subscriptionPlanId'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    /**
     * @return int|null
     */
    public function getSubscriptionPlanId(): ?int
    {
        return $this->subscriptionPlanId;
    }
}