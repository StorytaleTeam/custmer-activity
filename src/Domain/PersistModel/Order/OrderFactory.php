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
     * @param \DateTime|null $createdDate
     * @return AbstractOrder
     * @throws ApplicationException
     * @throws DomainException
     */
    public function build(Customer $customer, array $orderPositions, ?\DateTime $createdDate = null): AbstractOrder
    {
        $isOrderMustBeSubscriptionOrderSpecification = new IsOrderMustBeSubscriptionOrderSpecification();
        if ($isOrderMustBeSubscriptionOrderSpecification->isSatisfiedBy($orderPositions) === true) {
            $order = $this->buildOrderSubscription($customer, $orderPositions, $createdDate);
        } else {
            throw new DomainException(implode('. ', $isOrderMustBeSubscriptionOrderSpecification->getMessages()));
        }

        return $order;
    }

    public function buildOrderSubscription(
        Customer $customer,
        array $orderPositions,
        ?\DateTime $createdDate = null,
        ?int $oldId = null
    ): OrderSubscription
    {
        return new OrderSubscription($customer, OrderInterface::STATUS_NEW, $orderPositions, $createdDate, $oldId);
    }

    /**
     * @param Customer $customer
     * @param int $status
     * @param array $orderPositions
     * @param \DateTime|null $createdDate
     * @param int|null $oldId
     * @return OrderSubscription
     */
    public function buildOrderSubscriptionAll(
        Customer $customer,
        int $status,
        array $orderPositions,
        ?\DateTime $createdDate = null,
        ?int $oldId = null
    ): OrderSubscription
    {
        return new OrderSubscription($customer, $status, $orderPositions, $createdDate, $oldId);
    }
}