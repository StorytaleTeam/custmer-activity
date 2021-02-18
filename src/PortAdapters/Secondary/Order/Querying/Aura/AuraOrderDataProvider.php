<?php

namespace Storytale\SkeletonDev\PortAdapters\Secondary\Order\Querying\Aura;

use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;
use Storytale\SkeletonDev\Application\Query\TestOrder\OrderDataProvider;

class AuraOrderDataProvider extends AbstractAuraDataProvider
    implements OrderDataProvider
{
    public function find(int $id)
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'o.id',
                'o.customer_id AS customerId',
                'o.product_id as productId',
                'o.created_date AS createdDate',
                'o.sold_date as soldDate',
            ])
            ->from('orders AS o')
            ->where('o.id=:id')
            ->bindValue('id', $id);

        $response = $this->executeStatement($select->getStatement(), $select->getBindValues());
        $response = count($response) === 0 ? null : $response[0];

        return $response;
    }
}