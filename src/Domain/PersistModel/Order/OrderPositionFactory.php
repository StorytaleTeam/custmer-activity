<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\PersistModel\Product\ProductInterface;

class OrderPositionFactory
{
    /**
     * @param ProductInterface $product
     * @return OrderPosition
     */
    public function build(ProductInterface $product): OrderPosition
    {
        return new OrderPosition($product, $product->getProductName());
    }
}