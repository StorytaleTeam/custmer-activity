<?php

namespace Storytale\CustomerActivity\Application\Query\Customer;

interface OldCustomerDataProvider
{
    /**
     * @param int $count
     * @param int $page
     * @return array
     */
    public function getLikes(int $count, int $page): array;

    /**
     * @param int $count
     * @param int $page
     * @return array
     */
    public function getDownloads(int $count, int $page): array;
}