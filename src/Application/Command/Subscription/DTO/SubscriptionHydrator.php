<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;

/**
 * Class SubscriptionHydrator
 * @package Storytale\CustomerActivity\Application\Command\Subscription\DTO
 */
class SubscriptionHydrator
{
    /** @var SubscriptionPlanHydrator */
    private SubscriptionPlanHydrator $subscriptionPlanHydrator;

    /** @var MembershipHydrator */
    private MembershipHydrator $membershipHydrator;

    public function __construct(
        SubscriptionPlanHydrator $subscriptionPlanHydrator,
        MembershipHydrator $membershipHydrator
    )
    {
        $this->subscriptionPlanHydrator = $subscriptionPlanHydrator;
        $this->membershipHydrator = $membershipHydrator;
    }

        public function toArray(Subscription $subscription): array
    {
        return [
            'id' => $subscription->getId(),
            'status' => $subscription->getStatus(),
            'subscriptionPlan' =>
                $this->subscriptionPlanHydrator->toArray($subscription->getSubscriptionPlan()),
            'membership' => empty($subscription->getCurrentMembership()) ? null
                : $this->membershipHydrator->toArray($subscription->getCurrentMembership()),
        ];
    }
}