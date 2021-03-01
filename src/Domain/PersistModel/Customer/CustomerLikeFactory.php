<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

class CustomerLikeFactory
{
    public function create(Customer $customer, int $illustrationId): CustomerLike
    {
        return new CustomerLike($customer, $illustrationId);
    }
}