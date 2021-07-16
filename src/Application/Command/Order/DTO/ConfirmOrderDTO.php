<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

class ConfirmOrderDTO
{
    /** @var int|null */
    private ?int $customerId;

    /** @var int|null */
    private ?int $orderId;

    public function __construct(array $data)
    {
        $this->customerId = $data['customerId'] ?? null;
        $this->orderId = $data['orderId'] ?? null;
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
    public function getOrderId(): ?int
    {
        return $this->orderId;
    }
}