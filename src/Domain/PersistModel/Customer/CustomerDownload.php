<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class CustomerDownload extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var Customer|null */
    private ?Customer $customer;

    /** @var Subscription|null */
    private ?Subscription $subscription;

    /** @var int */
    private int $illustrationId;

    /** @var int|null */
    private ?int $reDownloadCount;

    /** @var \DateTime|null
     *
     */
    private ?\DateTime $lastDownloadDate;

    public function __construct(
        int $illustrationId, ?Customer $customer = null,
        ?\DateTime $lastDownloadDate = null, ?int $reDownloadCount = null
    )
    {
        $this->illustrationId = $illustrationId;
        $this->reDownloadCount = $reDownloadCount;
        $this->lastDownloadDate = $lastDownloadDate;
        $this->customer = $customer;
        $this->subscription = null;
        parent::__construct();
    }

    /**
     * @return int|null
     */
    public function getIllustrationId(): ?int
    {
        return $this->illustrationId;
    }

    public function reDownload(): void
    {
        $this->reDownloadCount++;
        $this->lastDownloadDate = new \DateTime();
    }

    /**
     * @return Subscription|null
     */
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    /**
     * @param Subscription $subscription
     * @throws DomainException
     */
    public function setSubscription(Subscription $subscription): void
    {
        if (empty($this->subscription)) {
            $this->subscription = $subscription;
        } else {
            throw new DomainException('Subscription already isset.');
        }
    }
}