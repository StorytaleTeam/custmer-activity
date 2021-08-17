<?php

namespace Storytale\CustomerActivity\Application\Query\Subscription;

/**
 * Interface OldSubscriptionDataProvider
 * @package Storytale\CustomerActivity\Application\Query\Subscription
 * @deprecated
 */
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
     * @param int $count
     * @param int $page
     * @return array
     */
    public function getMemberships(int $count, int $page): array;

    /**
     * @param int $postId
     * @return array
     */
    public function getMetaForPost(int $postId): array;
}