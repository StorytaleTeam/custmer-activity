<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Customer\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Customer\OldCustomerDataProvider;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraOldCustomerDataProvider extends AbstractAuraDataProvider
    implements OldCustomerDataProvider
{
    public const SKIPPED_CUSTOMERS = [
        10, 14977, 24, 28, 29, 3042, 36, 5, 7433,
    ];

    public const SKIPPED_ILLUSTRATIONS = [
        346, 365, 4621, 4622, 13381, 13384, 13386, 13387,
        10517, 10523, 10525, 10526, 10527, 10528,
        10529, 10530, 10531, 10532, 2027, 2028,
        2029, 2030, 221, 39, 4870, 4871, 4872,
        4873, 4874, 4875, 5045,
    ];

    public const SKIPPED_ILLUSTRATIONS_FOR_LIKE = [
        'product_346', 'product_365', 'product_4621', 'product_4622', 'product_13381', 'product_13384', 'product_13386', 'product_13387',
        'product_10517', 'product_10523', 'product_10525', 'product_10526', 'product_10527', 'product_10528',
        'product_10529', 'product_10530', 'product_10531', 'product_10532', 'product_2027', 'product_2028',
        'product_2029', 'product_2030', 'product_221', 'product_39', 'product_4870', 'product_4871', 'product_4872',
        'product_4873', 'product_4874', 'product_4875', 'product_5045',
    ];

    public function getLikes(int $count, int $page): array
    {
        $skippedIllustrationsForLike = "'". implode("', '", self::SKIPPED_ILLUSTRATIONS_FOR_LIKE) . "'";

        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_likebtn_vote')
            ->where('user_id NOT IN (' . implode(',', self::SKIPPED_CUSTOMERS) . ')')
            ->where("identifier NOT IN ($skippedIllustrationsForLike)")
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
            ->where('user_id NOT IN (' . implode(',', self::SKIPPED_CUSTOMERS) . ')')
            ->where('product_id NOT IN (' . implode(',', self::SKIPPED_ILLUSTRATIONS) . ')')
            ->limit($count)
            ->page($page)
            ->orderBy(['id']);

        return $this->executeStatement($select->getStatement(), $select->getBindValues());
    }

    public function getCanceledNewsletter(int $count, int $page): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['*'])
            ->from('wp_newsletter')
            ->where('status = :cancelStatus')
            ->limit($count)
            ->page($page)
            ->orderBy(['id'])
            ->bindValue('cancelStatus', 'U');

        return $this->executeStatement($select->getStatement(), $select->getBindValues());
    }
}