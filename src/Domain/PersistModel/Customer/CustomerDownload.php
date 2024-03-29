<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class CustomerDownload extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var Customer|null */
    private ?Customer $customer;

    /** @var Membership|null */
    private ?Membership $membership;

    /** @var int */
    private int $illustrationId;

    /** @var bool */
    private bool $isFree;

    /** @var int|null */
    private ?int $reDownloadCount;

    /** @var \DateTime|null
     *
     */
    private ?\DateTime $lastDownloadDate;

    public function __construct(
        int $illustrationId, bool $isFree,
        ?Customer $customer = null,
        ?\DateTime $lastDownloadDate = null, ?int $reDownloadCount = null
    )
    {
        $this->illustrationId = $illustrationId;
        $this->isFree = $isFree;
        $this->reDownloadCount = $reDownloadCount;
        $this->lastDownloadDate = $lastDownloadDate;
        $this->customer = $customer;
        $this->membership = null;
        parent::__construct($lastDownloadDate);
    }

    /**
     * @return bool
     */
    public function isFree(): bool
    {
        return $this->isFree;
    }

    /**
     * @return int|null
     */
    public function getIllustrationId(): ?int
    {
        return $this->illustrationId;
    }

    public function reDownload(?CustomerDownload $customerDownload = null): void
    {
        $this->reDownloadCount++;
        if ($customerDownload instanceof CustomerDownload) {
            $this->lastDownloadDate = $customerDownload->lastDownloadDate;
        } else {
            $this->lastDownloadDate = new \DateTime();
        }
    }

    /**
     * @return Membership|null
     */
    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    /**
     * @param Membership $membership
     * @throws DomainException
     */
    public function setMembership(Membership $membership): void
    {
        if (empty($this->membership)) {
            $this->membership = $membership;
        } else {
            throw new DomainException('Membership already isset.');
        }
    }
}