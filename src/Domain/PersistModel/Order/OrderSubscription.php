<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

class OrderSubscription extends AbstractOrder
{
    /** @var Subscription|null */
    private ?Subscription $subscription;

    public function __construct(Customer $customer, int $status, array $orderPositions, ?\DateTime $createdDate = null)
    {
        $this->subscription = null;
        parent::__construct($customer, $status, $orderPositions, $createdDate);
    }

    /**
     * @return Subscription|null
     */
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function assignSubscription(Subscription $subscription): void
    {
        if ($this->subscription instanceof Subscription) {
            throw new DomainException('Subscription already exist in order ' . $this->id);
        }
        $this->subscription = $subscription;
    }

    public function addProduct($product): void
    {
        throw new DomainException('You cannot add products for an OrderSubscription.');
    }

    /**
     * @return SubscriptionPlan|null
     * @throws DomainException
     */
    public function getSubscriptionPlan(): ?SubscriptionPlan
    {
        $subscriptionPlan = null;
        $orderPosition = $this->orderPositions[0] ?? null;
        if ($orderPosition instanceof OrderPosition) {
            $subscriptionPlan = $orderPosition->getProduct();
            if (!$subscriptionPlan instanceof SubscriptionPlan) {
                throw new DomainException('Invalid productType in OrderSubscription. OrderId ' . $this->id);
            }
        }

        return $subscriptionPlan;
    }

}