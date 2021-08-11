<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

/**
 * Interface MembershipRepository
 * @package Storytale\CustomerActivity\Domain\PersistModel\Subscription
 * @deprecated
 */
interface MembershipRepository
{
    /**
     * @param int $id
     * @return Membership|null
     */
    public function get(int $id): ?Membership;

    /**
     * @param int $oldId
     * @return Membership|null
     */
    public function getByOldId(int $oldId): ?Membership;

    /**
     * @param Membership $membership
     */
    public function save(Membership $membership): void;
}