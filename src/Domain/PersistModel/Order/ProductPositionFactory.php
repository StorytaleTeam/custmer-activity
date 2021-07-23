<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

class ProductPositionFactory
{
    public function build($product): ?ProductPosition
    {
        $productPosition = null;
        if ($product instanceof SubscriptionPlan) {
            $productPosition = $this->buildFromSubscriptionPlan($product);
        }

        return $productPosition;
    }

    public function buildFromSubscriptionPlan(SubscriptionPlan $subscriptionPlan): ProductPosition
    {
        return new ProductPosition(
            $subscriptionPlan->getName(),
            ProductPositionsService::PRODUCT_TYPE_SUBSCRIPTION_PLAN,
            $subscriptionPlan->getId(),
            $subscriptionPlan->getPrice(),
            1
        );
    }
}