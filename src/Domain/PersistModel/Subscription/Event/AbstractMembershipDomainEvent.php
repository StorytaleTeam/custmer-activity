<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription\Event;

use Storytale\Contracts\Domain\AbstractDomainEvent;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;

abstract class AbstractMembershipDomainEvent extends AbstractDomainEvent
{
    /** @var Membership */
    private Membership $membership;

    public function __construct(Membership $membership)
    {
        $this->membership = $membership;
    }

    /**
     * @return Membership
     */
    public function getMembership(): Membership
    {
        return $this->membership;
    }
}