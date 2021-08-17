<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Subscription\OldSubscriptionDataProvider;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraOldSubscriptionDataProvider extends AbstractAuraDataProvider
    implements OldSubscriptionDataProvider
{
    public function getOrders(int $count, int $page): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_posts')
            ->orderBy(['ID'])
            ->where('post_type = \'shop_order\'')
            ->limit($count)
            ->page($page);

        return $this->executeStatement($select->getStatement(), $select->getBindValues());
    }

    public function getOrderProducts(int $oldOrderId): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_wc_order_product_lookup')
            ->where('order_id = :orderId')
            ->bindValue('orderId', $oldOrderId);

        $response = $this->executeStatement($select->getStatement(), $select->getBindValues());

        return count($response) > 0 ?
            $response[0] : [];
    }

    public function getSubscriptions(int $count, int $page): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_posts')
            ->orderBy(['ID'])
            ->where('post_type = \'ywsbs_subscription\'')
            ->limit($count)
            ->page($page);

        return $this->executeStatement($select->getStatement(), $select->getBindValues());
    }

    public function getMemberships(int $count, int $page): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_posts')
            ->orderBy(['ID'])
            ->where('post_type = \'ywcmbs-membership\'')
            ->limit($count)
            ->page($page);

        return $this->executeStatement($select->getStatement(), $select->getBindValues());
    }

    public function getMetaForPost(int $postId): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_postmeta')
            ->where('post_id = :postId')
            ->bindValue('postId', $postId);

        return $this->executeStatement($select->getStatement(), $select->getBindValues());
    }
}