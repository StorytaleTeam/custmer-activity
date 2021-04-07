<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerDownload;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class Subscription extends AbstractEntity
{
    public const STATUS_NEW = 1;
    public const STATUS_ACTIVE = 2;
    public const STATUS_STOPPED = 3;

    /** @var int */
    private int $id;

    /** @var int */
    private int $status;

    /** @var Customer */
    private Customer $customer;

    /** @var SubscriptionPlan */
    private SubscriptionPlan $subscriptionPlan;

    /** @var array|null */
    private $memberships;

    /** @var int */
    private int $currentMembershipCycle;

    /** @var bool */
    private bool $autoRenewal;

    /** @var string|null */
    private ?string $paddleId;

    /**
     * Subscription constructor.
     * @param Customer $customer
     * @param SubscriptionPlan $subscriptionPlan
     * @param int $status
     * @param int $currentMembershipCycle
     * @param bool $autoRenewal
     * @param string|null $paddleId
     */
    public function __construct(
        Customer $customer, SubscriptionPlan $subscriptionPlan,
        int $status, int $currentMembershipCycle, bool $autoRenewal,
        ?string $paddleId = null
    )
    {
        $this->customer = $customer;
        $this->subscriptionPlan = $subscriptionPlan;
        $this->status = $status;
        $this->currentMembershipCycle = $currentMembershipCycle;
        $this->autoRenewal = $autoRenewal;
        $this->paddleId = $paddleId;
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCurrentMembership(): ?Membership
    {
        $currentMembership = null;
        $nowDate = new \DateTime();

        /** @var Membership $membership */
        foreach ($this->memberships as $membership) {
            if (
                $membership->getCycleNumber() === $this->currentMembershipCycle
                && $membership->getEndDate() > $nowDate
            ) {
                $currentMembership = $membership;
                break;
            }
        }

        return $currentMembership;
    }

    public function activate(): void
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @param CustomerDownload $customerDownload
     * @throws DomainException
     */
    public function newDownload(CustomerDownload $customerDownload): void
    {
        $currentMembership = $this->getCurrentMembership();
        if ($currentMembership instanceof Membership) {
            $currentMembership->newDownload($customerDownload);
        } else {
            throw new DomainException('There is no active membership for this subscription.');
        }
    }

    /**
     * @return SubscriptionPlan
     */
    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function addMembership(Membership $membership): void
    {
        /** @todo нужно проверять доступен ли тарифный план */
        if ($this->autoRenewal) {
            $membership->paid();
            $this->memberships[] = $membership;
            if (!$this->getCurrentMembership() instanceof Membership) {
                $this->startNewMembership($membership);
            }
        } else {
            /** @todo need alert to manager */
            throw new DomainException('There was an attempt to add a Membership to a stopped Subscription ' . $this->id);
        }
    }

    public function expireMembership(): void
    {
        $currentMembership = $this->getCurrentMembership();
        if ($currentMembership instanceof Membership) {
            $currentMembership->expire();
        }

        $nextMembership = null;
        /** @var Membership $membership */
        foreach ($this->memberships as $membership) {
            if (
                $membership->getStatus() === Membership::STATUS_PAID
                && $membership->getStartDate() === null
                && $membership->getEndDate() === null
                && $membership->getCycleNumber() === null
            ) {
                $nextMembership = $membership;
                break;
            }
        }

        if ($nextMembership instanceof $membership) {
            $this->startNewMembership($membership);
        } else if (!$this->autoRenewal) {
            /** нужно убедиться что не будут списываться деньги */
            $this->status = self::STATUS_STOPPED;
        }
    }

    public function startNewMembership(Membership $membership)
    {
        $this->currentMembershipCycle++;
        $membership->activate($this->currentMembershipCycle);
    }

    public function isAutoRenewal(): bool
    {
        return $this->autoRenewal;
    }

    public function unsubscribe()
    {
        $this->autoRenewal = false;
        if ($this->status === self::STATUS_NEW) {
            $this->cancel();
        }
    }

    public function cancel()
    {
        $this->status = self::STATUS_STOPPED;
        $this->autoRenewal = false;
    }

    /**
     * @param string $paddleId
     * @throws DomainException
     */
    public function initPaddleId(string $paddleId): void
    {
        if ($this->paddleId === null) {
            $this->paddleId = $paddleId;
        } else {
            throw new DomainException('PaddleId already init in subscription '  . $this->id);
        }
    }

    /**
     * @return string|null
     */
    public function getPaddleId(): ?string
    {
        return $this->paddleId;
    }
}