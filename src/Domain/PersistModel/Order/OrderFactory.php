<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;

class OrderFactory
{
    public function build(Customer $customer, ?\DateTime $createdDate = null): Order
    {
        return new Order($customer, Order::STATUS_NEW, $createdDate);
    }
}