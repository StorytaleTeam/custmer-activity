<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;

class OrderFactory
{
    public function build(Customer $customer): Order
    {
        return new Order($customer, 1);
    }
}