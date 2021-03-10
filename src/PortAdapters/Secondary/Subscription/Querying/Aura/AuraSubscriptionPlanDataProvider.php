<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionPlanBasic;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionPlanDataProvider;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraSubscriptionPlanDataProvider extends AbstractAuraDataProvider
    implements SubscriptionPlanDataProvider
{
    public function find(int $id): ?SubscriptionPlanBasic
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'sp.id',
                'sp.created_date' => 'createdDate',
                'sp.name',
                'sp.price',
                'sp.status',
                'sp.duration_count' => 'durationCount',
                'sp.duration_label' => 'durationLabel',
                'sp.download_limit' => 'downloadLimit',
            ])
            ->where('sp.id = :id')
            ->bindValue('id', $id)
            ->from('subscription_plans AS sp');

        $response = $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionPlanBasic::class);;
        $response = count($response) > 0 ? $response[0] : null;

        return $response;
    }

    public function findListForAdmin(): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'sp.id',
                'sp.created_date' => 'createdDate',
                'sp.name',
                'sp.price',
                'sp.status',
                'sp.duration_count' => 'durationCount',
                'sp.duration_label' => 'durationLabel',
                'sp.download_limit' => 'downloadLimit',
            ])
            ->from('subscription_plans AS sp');

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionPlanBasic::class);
    }

    public function findListForCustomer(): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'sp.id',
                'sp.created_date' => 'createdDate',
                'sp.name',
                'sp.price',
                'sp.duration_count' => 'durationCount',
                'sp.duration_label' => 'durationLabel',
                'sp.download_limit' => 'downloadLimit',
            ])
            ->from('subscription_plans AS sp')
            ->where('sp.status = :statusPublic')
            ->bindValue('statusPublic', SubscriptionPlan::STATUS_PUBLIC);

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionPlanBasic::class);
    }
}