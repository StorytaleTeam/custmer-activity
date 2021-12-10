<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription\Specification;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\TimeRange;
use Storytale\CustomerActivity\Domain\SpecificationInterface;

class IsValidDurationForSubscriptionPlanSpecification
{
    private array $messages;

    public function __construct()
    {
        $this->messages = [];
    }

    public function isSatisfiedBy(TimeRange $duration, TimeRange $chargePeriod): bool
    {
        if ($duration->getLabel() !== $chargePeriod->getLabel()) {
            $this->messages[] = 'Duration label and ChargePeriod label mast match.';
            return false;
        }
        if ($duration->getCount() > $chargePeriod->getCount()) {
            $this->messages[] = 'Duration count should not be more than the ChargePeriod.';
            return false;
        }
        if (($chargePeriod->getCount() % $duration->getCount()) !== 0) {
            $this->messages[] = 'ChargePeriod must be a multiple of the Duration.';
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getMessages(): string
    {
        return implode('. ', $this->messages);
    }
}