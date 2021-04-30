<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Illustration\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Illustration\CustomerActivityWithIllustrationBasic;
use Storytale\CustomerActivity\Application\Query\Illustration\IllustrationDataProvider;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraIllustrationDataProvider extends AbstractAuraDataProvider
    implements IllustrationDataProvider
{
    public function getActivityForCustomer(int $customerId, array $illustrationIds): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'COALESCE(cl.illustration_id, cd.illustration_id)' => 'illustrationId',
                'COALESCE(cl.customer_id, cd.customer_id)' => 'customerId',
                'CASE
                    WHEN cl.illustration_id >= 1 THEN true
                    ELSE false
                END' => 'isLiked',
                'CASE
                    WHEN cd.illustration_id >= 1 THEN true
                    ELSE false
                END' => 'isDownloaded',
            ])
            ->from('customer_likes AS cl')
            ->join(
                'FULL', 'customer_downloads AS cd',
                '(cl.illustration_id = cd.illustration_id AND cl.customer_id = cd.customer_id)'
            )
            ->where('(cl.customer_id = :customerId OR cd.customer_id = :customerId)')
            ->bindValue('customerId', $customerId);

        $whereIllustrationIdsImplode = implode(',', array_fill(0, sizeof($illustrationIds), '?'));
        $whereIllustrationIds = "(cl.illustration_id IN ($whereIllustrationIdsImplode) OR cd.illustration_id IN ($whereIllustrationIdsImplode))";
        $select->where(...array_merge([$whereIllustrationIds], $illustrationIds, $illustrationIds));

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), CustomerActivityWithIllustrationBasic::class);
    }
}