<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Application\Command\Order\DTO\ProductPositionDTO;

interface ProductPositionsService
{
    public const PRODUCT_TYPE_SUBSCRIPTION_PLAN = 'subscriptionPlan';

    public const SUPPORTED_PRODUCT_TYPES = [
        self::PRODUCT_TYPE_SUBSCRIPTION_PLAN,
    ];

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