<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Product;

interface ProductInterface
{
    public const PRODUCT_SUBSCRIPTION_PLAN = 'subscription_plan';

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @return float
     */
    public function getTotalPrice(): float;

    /**
     * @return string
     */
    public function getProductName(): string;
}