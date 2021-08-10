<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Application\Command\Product\DTO\ProductHydrator;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderPosition;

class OrderPositionHydrator
{
    /** @var ProductHydrator */
    private ProductHydrator $productHydrator;

    public function __construct(ProductHydrator $productHydrator)
    {
        $this->productHydrator = $productHydrator;
    }

    public function toArray(OrderPosition $orderPosition): array
    {
        return [
            'id' => $orderPosition->getId(),
            'displayName' => $orderPosition->getDisplayName(),
            'product' => $this->productHydrator->toArray($orderPosition->getProduct()),
        ];
    }
}