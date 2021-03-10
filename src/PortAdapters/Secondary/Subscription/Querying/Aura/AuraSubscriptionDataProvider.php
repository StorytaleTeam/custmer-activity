<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionBasic;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionDataProvider;
use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraSubscriptionDataProvider extends AbstractAuraDataProvider
    implements SubscriptionDataProvider
{
    public function findAllByCustomer(int $customerId, int $count, int $page): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                's.id',
                's.customer_id' => 'customerId',
                's.subscription_plan_id' => 'subscriptionPlanId',
                's.created_date' => 'createdDate',
                's.duration_count' => 'durationCount',
                's.duration_label' => 'durationLabel',
                's.name',
                's.price',
                's.status',
                's.start_date' => 'startDate',
                's.end_date' => 'endDate',
            ])
            ->from('subscriptions AS s')
            ->where('s.customer_id = :customerId')
            ->limit($count)
            ->offset($count * ($page - 1))
            ->bindValue('customerId' , $customerId);

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionBasic::class);
    }

    public function findActualForCustomer(int $customerId): ?SubscriptionBasic
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                's.id',
                's.customer_id'                     => 'customerId',
                's.subscription_plan_id'            => 'subscriptionPlanId',
                's.created_date'                    => 'createdDate',
                's.name',
                's.duration_count' => 'durationCount',
                's.duration_label' => 'durationLabel',
                's.price',
                's.download_limit'                  => 'downloadLimit',
                's.download_limit - (SELECT count(*) from customer_downloads 
                AS cd where cd.subscription_id = s.id)' => 'downloadRemaining',
                's.status',
                's.start_date'                      => 'startDate',
                's.end_date'                        => 'endDate',
            ])
            ->from('subscriptions AS s')
            ->where('s.customer_id = :customerId')
            ->where('s.start_date >= :nowDate')
            ->where('s.end_date < :nowDate')
            ->bindValue('customerId' , $customerId)
            ->bindValue('nowDate', new \DateTime());

        $response =  $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionBasic::class);
        if (count($response) > 1) {
            throw new DomainException('multiple actual subscriptions found for customerId: ' . $customerId);
        }

        return count($response) === 0 ? null : $response[0];
    }

    public function findList(int $count, int $page, ?array $params = null): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                's.id',
                's.customer_id' => 'customerId',
                's.subscription_plan_id' => 'subscriptionPlanId',
                's.created_date' => 'createdDate',
                's.duration_count' => 'durationCount',
                's.duration_label' => 'durationLabel',
                's.download_limit'                  => 'downloadLimit',
                's.download_limit - (SELECT count(*) from customer_downloads 
                AS cd where cd.subscription_id = s.id)' => 'downloadRemaining',
                's.name',
                's.price',
                's.status',
                's.start_date' => 'startDate',
                's.end_date' => 'endDate',
            ])
            ->from('subscriptions AS s')
            ->limit($count)
            ->offset($count * ($page - 1));

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), SubscriptionBasic::class);
    }
}