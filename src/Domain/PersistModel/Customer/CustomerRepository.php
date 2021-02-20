<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

interface CustomerRepository
{
    /**
     * @param Customer $customer
     */
    public function save(Customer $customer):void;

    /**
     * @param int $id
     * @return Customer|null
     */
    public function get(int $id): ?Customer;
}