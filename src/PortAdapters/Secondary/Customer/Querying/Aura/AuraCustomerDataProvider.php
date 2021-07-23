<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Customer\Querying\Aura;

use Storytale\CustomerActivity\Application\Query\Customer\CustomerBasic;
use Storytale\CustomerActivity\Application\Query\Customer\CustomerDataProvider;
use Storytale\CustomerActivity\Application\Query\Customer\CustomerDownloadBasic;
use Storytale\CustomerActivity\Application\Query\Customer\CustomerLikeBasic;
use Storytale\PortAdapters\Secondary\DataBase\Sql\StorytaleTeam\AbstractAuraDataProvider;

class AuraCustomerDataProvider extends AbstractAuraDataProvider
    implements CustomerDataProvider
{
    public function find(int $id): ?CustomerBasic
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'c.id',
                'c.email',
                'c.name',
                'c.createdDate',
            ])
            ->from('customers AS c')
            ->where('u.id = :customerId')
            ->bindValue('customerId', $id);

        $response = $this->executeStatement($select->getStatement(), $select->getBindValues(), CustomerBasic::class);
        $response = count($response) === 0 ? null : $response[0];

        return $response;
    }

    public function findCustomerLikes(int $customerId): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'cl.id',
                'cl.customer_id' => 'customerId',
                'cl.illustration_id' => 'illustrationId',
                'cl.created_date' => 'createdDate',

            ])
            ->from('customer_likes AS cl')
            ->where('cl.customer_id = :customerId')
            ->orderBy(['cl.created_date DESC'])
            ->bindValue('customerId', $customerId);

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), CustomerLikeBasic::class);
    }

    public function findCustomerDownloads(int $customerId): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'cd.id',
                'cd.customer_id' => 'customerId',
                'cd.illustration_id' => 'illustrationId',
                'cd.last_download_date' => 'lastDownloadDate',
                'cd.re_download_count' => 'reDownloadCount',
            ])
            ->from('customer_downloads AS cd')
            ->where('cd.customer_id = :customerId')
            ->orderBy(['cd.last_download_date DESC'])
            ->bindValue('customerId', $customerId);

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), CustomerDownloadBasic::class);
    }

    public function findListForAdmin(int $count, int $page, ?array $params = null): array
    {
        $select = $this->queryFactory
            ->newSelect()
            ->cols([
                'c.id',
                'c.email',
                'c.name',
                'c.created_date' => 'createdDate',
            ])
            ->from('customers AS c')
            ->limit($count)
            ->offset($count * ($page-1));

        return $this->executeStatement($select->getStatement(), $select->getBindValues(), CustomerBasic::class);
    }
}