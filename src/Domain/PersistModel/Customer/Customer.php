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

    public function __construct(
        int $id, string $email, bool $subscriptionAutoRenewal,
        ?string $name = null
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->subscriptionAutoRenewal = $subscriptionAutoRenewal;
        $this->name = $name;
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
        $nowDate = new \DateTime();
        /** @var Subscription $subscription */
        foreach ($this->subscriptions as $subscription) {
            if ($subscription->getStatus() === Subscription::STATUS_ACTIVE) {
                if (
                    ($subscription->getStartDate() instanceof \DateTime) && $subscription->getStartDate() <= $nowDate &&
                    ($subscription->getEndDate() instanceof \DateTime) && $subscription->getEndDate() > $nowDate
                ) {
                    return $subscription;
                }
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
}