<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Order;

use Storytale\CustomerActivity\Application\Command\Order\DTO\ProductPositionDTO;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPosition;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPositionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPositionsService;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;

class StorytaleProductPositionsService implements ProductPositionsService
{
    /** @var SubscriptionPlanRepository */
    private SubscriptionPlanRepository $subscriptionPlanRepository;

    /** @var ProductPositionFactory */
    private ProductPositionFactory $productPositionFactory;

    public function __construct(
        SubscriptionPlanRepository $subscriptionPlanRepository,
        ProductPositionFactory $productPositionFactory
    )
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->productPositionFactory = $productPositionFactory;
    }

    public function getProductByProductPosition(ProductPosition $productPosition)
    {
        $product = null;
        if ($productPosition->getProductType() === ProductPositionsService::PRODUCT_TYPE_SUBSCRIPTION_PLAN) {
            $product = $this->subscriptionPlanRepository->get($productPosition->getProductId());
        }

        return $product;
    }

    public function getProductPositionByDTO(ProductPositionDTO $productPositionDTO): ?ProductPosition
    {
        $productPosition = null;
        switch ($productPositionDTO->getProductType()) {
            case ProductPositionsService::PRODUCT_TYPE_SUBSCRIPTION_PLAN:
                $product = $this->subscriptionPlanRepository->get($productPositionDTO->getProductId());
                if (!$product instanceof SubscriptionPlan) {
                    throw new ValidationException("Plan with this id not found.");
                }
                $productPosition = $this->productPositionFactory->buildFromSubscriptionPlan($product);
                break;
            default:
                throw new ValidationException('Unsupported product type given.');
        }

        return $productPosition;
    }
}