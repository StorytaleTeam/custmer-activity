<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

class CustomerLikeFactory
{
    public function create(Customer $customer, int $illustrationId, ?\DateTime $createdDate = null): CustomerLike
    {
        return new CustomerLike($customer, $illustrationId, $createdDate);
    }
}