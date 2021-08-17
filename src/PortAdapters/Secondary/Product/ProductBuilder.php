<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Product;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Product\IProductBuilder;
use Storytale\CustomerActivity\Domain\PersistModel\Product\ProductInterface;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;

class ProductBuilder implements IProductBuilder
{
    /** @var SubscriptionPlanRepository */
    private SubscriptionPlanRepository $subscriptionPlanRepository;

    public function __construct(SubscriptionPlanRepository $subscriptionPlanRepository)
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
    }

    public function build(string $productType, int $productId): ProductInterface
    {
        switch ($productType) {
            case ProductInterface::PRODUCT_SUBSCRIPTION_PLAN:
                $product = $this->buildSubscriptionPlanProduct($productId);
                break;
            default:
                throw new DomainException('Unsupported product type given.');
        }

        return $product;
    }

    /**
     * @param int $subscriptionPlanId
     * @return SubscriptionPlan|null
     * @throws DomainException
     */
    public function buildSubscriptionPlanProduct(int $subscriptionPlanId): ?SubscriptionPlan
    {
        $subscriptionPlan = $this->subscriptionPlanRepository->get($subscriptionPlanId);
        if (!$subscriptionPlan instanceof SubscriptionPlan) {
            throw new DomainException("Not found SubscriptionPlan with id $subscriptionPlanId");
        }

        return $subscriptionPlan;
    }

    /**
     * @param int $oldPlanId
     * @return SubscriptionPlan|null
     * @throws DomainException
     * @deprecated
     */
    public function buildSubscriptionPlanByOldId(int $oldPlanId): ?SubscriptionPlan
    {
        $plan = $this->subscriptionPlanRepository->getByOldId($oldPlanId);
        if (!$plan instanceof SubscriptionPlan) {
            throw new DomainException("Not found SubscriptionPlan with oldId $oldPlanId");
        }

        return $plan;
    }

}