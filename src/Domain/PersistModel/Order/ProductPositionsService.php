<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Application\Command\Order\DTO\ProductPositionDTO;

interface ProductPositionsService
{
    public const SUPPORTED_PRODUCT_TYPES = ['subscriptionPlan'];

    /**
     * @param ProductPositionDTO $productPositionDTO
     * @return ProductPosition|null
     */
    public function getProductPositionByDTO(ProductPositionDTO $productPositionDTO): ?ProductPosition;

    /**
     * @param ProductPosition $productPosition
     * @return mixed
     */
    public function getProductByProductPosition(ProductPosition $productPosition);
}