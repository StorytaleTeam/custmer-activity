<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;

/**
 * Class MembershipHydrator
 * @package Storytale\CustomerActivity\Application\Command\Subscription\DTO
 */
class MembershipHydrator
{
    public function toArray(Membership $membership): array
    {
        return [
            'id' => $membership->getId(),
            'status' => $membership->getStatus(),
            'startDate' => $this->dateFormat($membership->getStartDate()),
            'endDate' => $this->dateFormat($membership->getEndDate()),
            'cycleNumber' => $membership->getCycleNumber(),
        ];
    }

    private function dateFormat(?\DateTime $dateTime): ?string
    {
        $response = null;
        if ($dateTime instanceof \DateTime) {
            $response = $dateTime->format('Y-m-d H:i:s');
        }

        return $response;
    }
}