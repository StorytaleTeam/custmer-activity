<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription\Specification;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\SpecificationInterface;

class SubscriptionCanBeProlongatedSpecification implements SpecificationInterface
{
    public function isSatisfiedBy($candidate): bool
    {
        if (!$candidate instanceof Subscription) {
            throw new DomainException('Invalid instance of candidate given. Need instance of Subscription.');
        }


    }
}