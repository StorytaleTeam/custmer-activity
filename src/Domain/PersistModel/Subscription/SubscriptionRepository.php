<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

interface SubscriptionRepository
{
    /**
     * @param Subscription $subscription
     */
    public function save(Subscription $subscription): void;

    /**
     * @param int $id
     * @return Subscription|null
     */
    public function get(int $id): ?Subscription;

    /**
     * @return Subscription[]
     */
    public function getForProlongate(): array;
}