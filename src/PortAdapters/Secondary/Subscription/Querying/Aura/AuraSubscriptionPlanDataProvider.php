<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Querying\Aura;

use Aura\SqlQuery\Common\SelectInterface;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionPlanBasic;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionPlanDataProvider;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraSubscriptionPlanDataProvider extends AbstractAuraDataProvider
    implements SubscriptionPlanDataProvider
{
    private function prepareFulSelectForCustomer(): SelectInterface
    {
        return $this->queryFactory
            ->newSelect()
            ->cols([
                'sp.id',
                'sp.name',
                'sp.duration_count' => 'durationCount',
                'sp.duration_label' => 'durationLabel',
                'sp.download_limit' => 'downloadLimit',
                'sp.paddle_id'      => 'paddleId',
                'p.created_date'   => 'createdDate',
                'p.price',
            ])
            ->from('subscription_plans AS sp')
            ->join('LEFT', 'products AS p', 'p.id = sp.id')
            ->where('sp.status = :statusPublic')
            ->bindValue('statusPublic', SubscriptionPlan::STATUS_PUBLIC);
    }

    public function find(int $id): ?SubscriptionPlanBasic
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'sp.id',
                'sp.name',
                'sp.status',
                'sp.duration_count' => 'durationCount',
                'sp.duration_label' => 'durationLabel',
                'sp.download_limit' => 'downloadLimit',
                'sp.paddle_id' => 'paddleId',
                'p.created_date'   => 'createdDate',
                'p.price',
            ])
            ->where('sp.id = :id')
            ->bindValue('id', $id)
            ->from('subscription_plans AS sp')
            ->join('LEFT', 'products AS p', 'p.id = sp.id');

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
                'sp.name',
                'sp.status',
                'sp.duration_count' => 'durationCount',
                'sp.duration_label' => 'durationLabel',
                'sp.download_limit' => 'downloadLimit',
                'sp.paddle_id' => 'paddleId',
                'p.created_date'   => 'createdDate',
                'p.price',
            ])
            ->from('subscription_plans AS sp')
            ->join('LEFT', 'products AS p', 'p.id = sp.id');

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionPlanBasic::class);
    }

    public function findListForCustomer(): array
    {
        $select = $this->prepareFulSelectForCustomer();

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionPlanBasic::class);
    }

    public function findOneForCustomer(int $id): ?SubscriptionPlanBasic
    {
        $select = $this->prepareFulSelectForCustomer()
            ->where('sp.id = :id')
            ->bindValue('id', $id);

        $response = $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionPlanBasic::class);
        $response = count($response) > 0 ? $response[0] : null;

        return $response;
    }
}