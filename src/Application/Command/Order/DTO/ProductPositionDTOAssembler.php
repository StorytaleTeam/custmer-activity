<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPosition;

class ProductPositionDTOAssembler
{
    public function toArray(ProductPosition $productPosition): array
    {
        return [
            'productId' => $productPosition->getProductId(),
            'productType' => $productPosition->getProductType(),
            'displayName' => $productPosition->getDisplayName(),
            'price' => $productPosition->getPrice(),
            'count' => $productPosition->getCount(),
        ];
    }
}