<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Order\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Order\OrderBasic;
use Storytale\CustomerActivity\Application\Query\Order\OrderDataProvider;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraOrderDataProvider extends AbstractAuraDataProvider
    implements OrderDataProvider
{

    public function findListForCustomer(int $customerId): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'id',
                'status',
                'created_date' => 'createdDate',
                'total_price' => 'totalPrice',
            ])
            ->from('orders AS o')
            ->where('o.customer_id = :customerId')
            ->bindValue('customerId', $customerId);

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), OrderBasic::class);
    }

    public function findOneForCustomer(int $customerId, int $orderId): ?OrderBasic
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'id',
                'status',
                'created_date'              => 'createdDate',
                'total_price'               => 'totalPrice',
                '(SELECT json_agg (tmp)
                 FROM (
                    SELECT 
                        opp.id,
                        opp.price,
                        opp.count,
                        opp.display_name AS displayName
                    FROM order_product_positions AS opp
                    WHERE opp.order_id = o.id) AS tmp
                 )'                         => 'productPositions',
            ])
            ->from('orders AS o')
            ->where('o.customer_id = :customerId')
            ->where('o.id = :orderId')
            ->bindValue('customerId', $customerId)
            ->bindValue('orderId', $orderId);

        $response = $this->executeStatement($select->getStatement(), $select->getBindValues(), OrderBasic::class);
        $response = count($response) === 0 ? null : $response[0];

        return $response;
    }
}