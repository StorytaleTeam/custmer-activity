<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Order\Specification\IsOrderMustBeSubscriptionOrderSpecification;

class OrderFactory
{
    /**
     * @param Customer $customer
     * @param array $orderPositions
     * @return AbstractOrder
     * @throws DomainException
     * @throws ApplicationException
     */
    public function build(Customer $customer, array $orderPositions): AbstractOrder
    {
        $isOrderMustBeSubscriptionOrderSpecification = new IsOrderMustBeSubscriptionOrderSpecification();
        if ($isOrderMustBeSubscriptionOrderSpecification->isSatisfiedBy($orderPositions) === true) {
            $order = $this->buildOrderSubscription($customer, $orderPositions);
        } else {
            throw new DomainException(implode('. ', $isOrderMustBeSubscriptionOrderSpecification->getMessages()));
        }

        return $order;
    }

    public function buildOrderSubscription(Customer $customer, array $orderPositions): OrderSubscription
    {
        return new OrderSubscription($customer, OrderInterface::STATUS_NEW, $orderPositions);
    }
}