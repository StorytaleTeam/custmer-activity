<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class Customer extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var array */
    private $likes;

    /** @var array */
    private $downloads;

    /** @var array */
    private $subscriptions;

    /** @var string */
    private string $email;

    /** @var bool */
    private bool $subscriptionAutoRenewal;

    /** @var string|null */
    private ?string $name;

    /** @var int|null */
    private ?int $oldId;

    public function __construct(
        int $id, string $email, bool $subscriptionAutoRenewal,
        ?string $name = null, ?int $oldId = null
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->subscriptionAutoRenewal = $subscriptionAutoRenewal;
        $this->name = $name;
        $this->oldId = $oldId;
        parent::__construct();
    }

    /**
     * @param int $illustrationId
     * @return bool
     */
    public function isAlreadyDownloaded(int $illustrationId): bool
    {
        /** @var CustomerDownload $download */
        foreach ($this->downloads as $download) {
            if ($download->getIllustrationId() === $illustrationId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Subscription|null
     */
    public function getActualSubscription(): ?Subscription
    {
        /** @var Subscription $subscription */
        foreach ($this->subscriptions as $subscription) {
            if ($subscription->getStatus() === Subscription::STATUS_ACTIVE) {
                return $subscription;
            }
        }

        return null;
    }

    /**
     * @param CustomerDownload $newDownload
     * @return bool
     * @throws DomainException
     * @Annotation return true if is new download
     */
    public function trackDownload(CustomerDownload $newDownload): bool
    {
        $isNewDownload = true;

        /** @var CustomerDownload $download */
        foreach ($this->downloads as $download) {
            if ($download->getIllustrationId() === $newDownload->getIllustrationId()) {
                $download->reDownload();
                $isNewDownload = false;
                break;
            }
        }
        if ($isNewDownload) {
            $this->getActualSubscription()->newDownload($newDownload);
            $this->downloads[] = $newDownload;
        }

        return $isNewDownload;
    }

    public function like(CustomerLike $customerLike): bool
    {
        /** @var CustomerLike $like */
        foreach ($this->likes as $like) {
            if ($like->getIllustrationId() === $customerLike->getIllustrationId()) {
                return false;
            }
        }

        $this->likes[] = $customerLike;
        return true;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int|null
     */
    public function getOldId(): ?int
    {
        return $this->oldId;
    }
}