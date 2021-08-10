<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order\Specification;

use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderPosition;
use Storytale\CustomerActivity\Domain\PersistModel\Product\ProductInterface;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Specification\IsSubscriptionPlanCanCreateNewSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

class IsOrderMustBeSubscriptionOrderSpecification
{
    private array $messages;

    public function __construct()
    {
        $this->messages = [];
    }

    public function isSatisfiedBy($candidate): bool
    {
        $this->messages = [];

        if (!is_array($candidate)) {
            throw new ApplicationException('Invalid argument provided.');
        }
        if (count($candidate) !== 1) {
            $this->messages[] = 'OrderSubscription can contain only 1 product.';
            return false;
        }

        $orderPosition = $candidate[0] ?? null;
        if (!$orderPosition instanceof OrderPosition) {
            throw new ApplicationException('Invalid argument provided.');
        }
        $product = $orderPosition->getProduct();
        if (!$product instanceof ProductInterface) {
            throw new ApplicationException('Invalid argument provided.');
        }

        if (!$product instanceof SubscriptionPlan) {
            $this->messages[] = 'OrderSubscription can contain only SubscriptionPlan product.';
            return false;
        }

        $isSubscriptionPlanCanCreateNewSubscription = new IsSubscriptionPlanCanCreateNewSubscription();
        if ($isSubscriptionPlanCanCreateNewSubscription->isSatisfiedBy($product) !== true) {
            $this->messages = array_merge($this->messages, $isSubscriptionPlanCanCreateNewSubscription->getMessages());
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}