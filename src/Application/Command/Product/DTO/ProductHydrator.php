<?php

namespace Storytale\CustomerActivity\Application\Command\Product\DTO;

use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanDTOAssembler;
use Storytale\CustomerActivity\Domain\PersistModel\Product\ProductInterface;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

class ProductHydrator
{
    /** @var SubscriptionPlanDTOAssembler */
    private SubscriptionPlanDTOAssembler $subscriptionPlanDTOAssembler;

    public function __construct(SubscriptionPlanDTOAssembler $subscriptionPlanDTOAssembler)
    {
        $this->subscriptionPlanDTOAssembler = $subscriptionPlanDTOAssembler;
    }

    public function toArray(ProductInterface $product): array
    {
        $productData = [];
        switch (true) {
            case $product instanceof SubscriptionPlan:
                $productData = $this->subscriptionPlanDTOAssembler->toArray($product);
                break;
        }

        $productData['totalPrice'] = $product->getTotalPrice();
        $productData['productName'] = $product->getProductName();

        return $productData;
    }
}