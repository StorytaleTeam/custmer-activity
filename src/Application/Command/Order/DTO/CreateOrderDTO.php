<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

class CreateOrderDTO
{
    /** @var int|null */
    private ?int $customerId;

    /** @var array|null */
    private ?array $orderPositionsDTO;

    public function __construct(array $data)
    {
        $this->customerId = $data['customerId'] ?? null;

        $this->orderPositionsDTO = null;
        if (isset($data['orderPositions']) && is_array($data['orderPositions'])) {
            foreach ($data['orderPositions'] as $position) {
                if (is_array($position)) {
                    $this->orderPositionsDTO[] = new OrderPositionDTO($position);
                }
            }
        }
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    /**
     * @return OrderPositionDTO[]|null
     */
    public function getOrderPositionsDTO(): ?array
    {
        return $this->orderPositionsDTO;
    }
}