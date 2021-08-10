<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

interface OldSubscriptionDataProvider
{
    /**
     * @param int $count
     * @param int $page
     * @return array
     */
    public function getOrders(int $count, int $page): array;

    /**
     * @param int $oldOrderId
     * @return array
     */
    public function getOrderProducts(int $oldOrderId): array;

    /**
     * @param int $count
     * @param int $page
     * @return array
     */
    public function getSubscriptions(int $count, int $page): array;

    /**
     * @param int $postId
     * @return array
     */
    public function getMetaForPost(int $postId): array;
}