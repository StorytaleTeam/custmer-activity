<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

interface LikeRepository
{
    /**
     * @param int $customerId
     * @param int $illustrationId
     * @return CustomerLike|null
     */
    public function getByCustomerAndIllustration(int $customerId, int $illustrationId): ?CustomerLike;

    /**
     * @param CustomerLike $customerLike
     */
    public function delete(CustomerLike $customerLike): void;
}