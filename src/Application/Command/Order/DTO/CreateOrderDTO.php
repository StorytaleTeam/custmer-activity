<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

class CreateOrderDTO
{
    /** @var int|null */
    private ?int $customerId;

    /** @var array|null */
    private ?array $productPositionsDTO;

    public function __construct(array $data)
    {
        $this->customerId = $data['customerId'] ?? null;

        $this->productPositionsDTO = null;
        if (isset($data['productPositions']) && is_array($data['productPositions'])) {
            foreach ($data['productPositions'] as $position) {
                if (is_array($position)) {
                    $this->productPositionsDTO[] = new ProductPositionDTO($position);
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
     * @return array|null
     */
    public function getProductPositionsDTO(): ?array
    {
        return $this->productPositionsDTO;
    }
}