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

    /**
     * @param int $oldId
     * @return Customer|null
     */
    public function getByOldId(int $oldId): ?Customer;

    /**
     * @param string $email
     * @return Customer|null
     */
    public function getByEmail(string $email): ?Customer;
}