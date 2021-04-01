<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Querying\Aura;

use Aura\SqlQuery\Common\SelectInterface;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionBasic;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionDataProvider;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraSubscriptionDataProvider extends AbstractAuraDataProvider
    implements SubscriptionDataProvider
{
    private function prepareShortSelect(): SelectInterface
    {
        return $this->queryFactory
            ->newSelect()
            ->cols([
                's.id',
                's.customer_id'                 => 'customerId',
                's.subscription_plan_id'        => 'subscriptionPlanId',
                's.created_date'                => 'createdDate',
                's.status',

                'm.id'                          => 'membershipId',
                'm.start_date'                  => 'startDate',
                'm.end_date'                    => 'endDate',
                'm.amount_received'             => 'amountReceived',
                'm.download_limit - (SELECT count(*) from customer_downloads 
                AS cd where cd.membership_id = m.id)' => 'downloadRemaining',
                'm.download_limit'              => 'downloadLimit',

                'sp.name',
                'sp.price',
                'sp.id'                         => 'planId',
                'sp.duration_label'             => 'durationLabel',
                'sp.duration_count'             => 'durationCount',
            ])
            ->from('subscriptions AS s')
            ->join('LEFT', 'memberships AS m', 's.id = m.subscription_id '
                . 'AND s.current_membership_cycle = m.cycle_number')
            ->join('LEFT', 'subscription_plans AS sp', 's.subscription_plan_id = sp.id');
    }

    public function findOneForCustomer(int $subscriptionId, int $customerId): ?SubscriptionBasic
    {
        $select = $this->prepareShortSelect()
            ->where('s.id = :id')
            ->where('s.customer_id = :customerId')
            ->bindValues([
                'id' => $subscriptionId,
                'customerId' => $customerId,
            ]);

        $response = $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionBasic::class);

        return $response[0] ?? null;
    }

    public function findAllByCustomer(int $customerId, int $count, int $page): array
    {
        $select = $this->prepareShortSelect()
            ->where('s.customer_id = :customerId')
            ->limit($count)
            ->offset($count * ($page - 1))
            ->bindValue('customerId' , $customerId);

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionBasic::class);
    }

    public function findList(int $count, int $page, ?array $params = null): array
    {
        $select = $this->prepareShortSelect()
            ->limit($count)
            ->offset($count * ($page - 1));

        if (isset($params['subscriptionPlanId'])) {
            $select
                ->where('s.subscription_plan_id = :subscriptionPlanId')
                ->bindValue('subscriptionPlanId', $params['subscriptionPlanId']);
        }

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionBasic::class);
    }
}