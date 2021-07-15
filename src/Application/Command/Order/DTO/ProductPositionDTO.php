<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

class ProductPositionDTO
{
    /** @var string|null */
    private ?string $productType;

    /** @var int|null */
    private ?int $productId;

    /** @var int|null */
    private ?int $count;

    public function __construct(array $data)
    {
        $this->productType = $data['type'] ?? null;
        $this->productId = $data['id'] ?? null;
        $this->count = $data['count'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getProductType(): ?string
    {
        return $this->productType;
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }
}