<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Customer\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Customer\OldCustomerDataProvider;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraOldCustomerDataProvider extends AbstractAuraDataProvider
    implements OldCustomerDataProvider
{
    public function getLikes(int $count, int $page): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_likebtn_vote')
            ->limit($count)
            ->page($page)
            ->orderBy(['ID']);

        return $this->executeStatement($select->getStatement(), $select->getBindValues());
    }

    public function getDownloads(int $count, int $page): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_yith_wcmbs_downloads_log')
            ->limit($count)
            ->page($page)
            ->orderBy(['id']);

        return $this->executeStatement($select->getStatement(), $select->getBindValues());
    }
}