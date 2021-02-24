<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

class CustomerFactory
{
    public function createFromArray(array $data): Customer
    {
        return new Customer(
            $data['id'],
            $data['email'],
            true,
            $data['name']
        );
    }
}