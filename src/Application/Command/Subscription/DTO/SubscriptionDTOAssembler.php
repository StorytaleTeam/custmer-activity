<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;

/**
 * Class SubscriptionDTOAssembler
 * @package Storytale\CustomerActivity\Application\Command\Subscription\DTO
 * @todo rename to Hydrator
 */
class SubscriptionDTOAssembler
{
    /** @var SubscriptionPlanDTOAssembler */
    private SubscriptionPlanDTOAssembler $subscriptionPlanDTOAssembler;

    /** @var MembershipDTOAssembler */
    private MembershipDTOAssembler $membershipDTOAssembler;

    public function __construct(
        SubscriptionPlanDTOAssembler $subscriptionPlanDTOAssembler,
        MembershipDTOAssembler $membershipDTOAssembler
    )
    {
        $this->subscriptionPlanDTOAssembler = $subscriptionPlanDTOAssembler;
        $this->membershipDTOAssembler = $membershipDTOAssembler;
    }

        public function toArray(Subscription $subscription): array
    {
        return [
            'id' => $subscription->getId(),
            'status' => $subscription->getStatus(),
            'subscriptionPlan' =>
                $this->subscriptionPlanDTOAssembler->toArray($subscription->getSubscriptionPlan()),
            'membership' => empty($subscription->getCurrentMembership()) ? null
                : $this->membershipDTOAssembler->toArray($subscription->getCurrentMembership()),
        ];
    }
}