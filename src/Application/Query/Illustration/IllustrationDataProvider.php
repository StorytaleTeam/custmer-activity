<?php

namespace Storytale\CustomerActivity\Application\Query\Illustration;

interface IllustrationDataProvider
{
    /**
     * @param int $customerId
     * @param array $illustrationIds
     * @return CustomerActivityWithIllustrationBasic[]
     */
    public function getActivityForCustomer(int $customerId, array $illustrationIds): array;
}