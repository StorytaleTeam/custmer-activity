<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Product;

use Storytale\CustomerActivity\Domain\DomainException;

interface IProductBuilder
{
    public const SUPPORTED_PRODUCT_TYPES = [ProductInterface::PRODUCT_SUBSCRIPTION_PLAN];

    /**
     * @param string $productType
     * @param int $productId
     * @throws DomainException
     * @return ProductInterface
     */
    public function build(string $productType, int $productId): ProductInterface;
}