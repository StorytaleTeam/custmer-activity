<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;

interface OrderSubscriptionRepository
{
    /**
     * @param Subscription $subscription
     * @return OrderSubscription|null
     */
    public function getBySubscription(Subscription $subscription): ?OrderSubscription;
}