<?php

namespace Storytale\CustomerActivity\Application\Command\Product\DTO;

use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanHydrator;
use Storytale\CustomerActivity\Domain\PersistModel\Product\ProductInterface;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

class ProductHydrator
{
    /** @var SubscriptionPlanHydrator */
    private SubscriptionPlanHydrator $subscriptionPlanHydrator;

    public function __construct(SubscriptionPlanHydrator $subscriptionPlanHydrator)
    {
        $this->subscriptionPlanHydrator = $subscriptionPlanHydrator;
    }

    public function toArray(ProductInterface $product): array
    {
        $productData = [];
        switch (true) {
            case $product instanceof SubscriptionPlan:
                $productData = $this->subscriptionPlanHydrator->toArray($product);
                break;
        }

        $productData['price'] = $product->getPrice();
        $productData['productName'] = $product->getProductName();

        return $productData;
    }
}