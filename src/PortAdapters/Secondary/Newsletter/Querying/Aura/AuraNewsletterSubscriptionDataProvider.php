<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Newsletter\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Newsletter\NewsletterSubscriptionBasic;
use Storytale\CustomerActivity\Application\Query\Newsletter\NewsletterSubscriptionDataProvider;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraNewsletterSubscriptionDataProvider extends AbstractAuraDataProvider
    implements NewsletterSubscriptionDataProvider
{
    public function count(array $params): int
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols(['count(*)'])
            ->from('newsletter_subscriptions AS ns')
            ->where('ns.is_active = true');
        if (isset($params['type'])) {
            $select
                ->where('ns.type = :type')
                ->bindValue('type', $params['type']);
        }

        $response = $this->executeStatement($select->getStatement(), $select->getBindValues());

        return $response[0]['count'] ?? 0;
    }

    public function getList(array $params): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'ns.email',
                'ns.type',
                'ns.uuid',
            ])
            ->from('newsletter_subscriptions AS ns')
            ->where('ns.is_active = true')
            ->orderBy(['id']);
        if (isset($params['type'])) {
            $select
                ->where('ns.type = :type')
                ->bindValue('type', $params['type']);
        }
        if (isset($params['count'])) {
            $select->limit($params['count']);
            if (isset($params['page'])) {
                $select->offset($params['count'] * ($params['page']-1));
            }
        }

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), NewsletterSubscriptionBasic::class);
    }

    public function getListForCustomer(int $customerId): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'ns.email',
                'ns.type',
                'ns.uuid',
                'ns.is_active' => 'isActive',
            ])
            ->from('newsletter_subscriptions AS ns')
            ->where('ns.customer_id = :customerId')
            ->bindValue('customerId', $customerId)
            ->orderBy(['id']);

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), NewsletterSubscriptionBasic::class);
    }
}