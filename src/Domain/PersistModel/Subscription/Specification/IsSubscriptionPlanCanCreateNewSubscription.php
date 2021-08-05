<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription\Specification;

use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;

class IsSubscriptionPlanCanCreateNewSubscription
{
    /** @var array */
    private array $messages;

    public function __construct()
    {
        $this->messages = [];
    }

    public function isSatisfiedBy($candidate): bool
    {
        if (!$candidate instanceof SubscriptionPlan) {
            throw new ApplicationException('Invalid argument provided.');
        }

        if ($candidate->getStatus() !== SubscriptionPlan::STATUS_PUBLIC) {
            $this->messages[] = 'Only subscription plan with status ' . SubscriptionPlan::STATUS_PUBLIC . ' can be use for new subscriptions.';
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}