<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;

    class SubscriptionDTOAssembler
{
    /** @var SubscriptionPlanDTOAssembler */
    private SubscriptionPlanDTOAssembler $subscriptionPlanDTOAssembler;

    public function __construct(SubscriptionPlanDTOAssembler $subscriptionPlanDTOAssembler)
    {
        $this->subscriptionPlanDTOAssembler = $subscriptionPlanDTOAssembler;
    }

        public function toArray(Subscription $subscription): array
    {
        return [
            'id' => $subscription->getId(),
            'name' => $subscription->getName(),
            'price' => $subscription->getPrice(),
            'subscriptionPlan' =>
                $this->subscriptionPlanDTOAssembler->toArray($subscription->getSubscriptionPlan()),
        ];
    }
}