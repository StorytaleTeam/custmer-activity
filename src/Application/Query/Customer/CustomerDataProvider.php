<?php

namespace Storytale\CustomerActivity\Application\Query\Customer;

interface CustomerDataProvider
{
    /**
     * @param int $id
     * @return CustomerBasic|null
     */
    public function find(int $id): ?CustomerBasic;

    /**
     * @param int $customerId
     * @return CustomerLikeBasic[]
     */
    public function findCustomerLikes(int $customerId): array;

    /**
     * @param int $customerId
     * @return CustomerDownloadBasic[]
     */
    public function findCustomerDownloads(int $customerId): array;

    /**
     * @param int $count
     * @param int $page
     * @param array|null $params
     * @return CustomerBasic[]
     */
    public function findListForAdmin(int $count, int $page, ?array $params = null): array;

    /**
     * @return int
     */
    public function count(): int;
}