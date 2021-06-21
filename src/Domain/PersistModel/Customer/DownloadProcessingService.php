<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;

class DownloadProcessingService
{
    /** @var CustomerDownloadFactory */
    private CustomerDownloadFactory $customerDownloadFactory;

    public function __construct(CustomerDownloadFactory $customerDownloadFactory)
    {
        $this->customerDownloadFactory = $customerDownloadFactory;
    }

    /**
     * @param Customer $customer
     * @param int $illustrationId
     * @return bool
     * @throws DomainException
     */
    public function getDownloadPass(Customer $customer, int $illustrationId): bool
    {
        $actualSubscribe = $customer->getActualSubscription();
        if (!$actualSubscribe instanceof Subscription) {
            throw new DomainException('You have not actual subscription.', 109001001);
        }
        $currentMembership = $actualSubscribe->getCurrentMembership();
        if (!$currentMembership instanceof Membership) {
            throw new DomainException('You have not actual membership.', 109001002);
        }

        $isAlreadyDownloaded = $customer->isAlreadyDownloaded($illustrationId);
        if (!$isAlreadyDownloaded) {
            if ($currentMembership->getDownloadRemaining() < 1) {
                throw new DomainException('Downloads limit reached.', 109001003);
            }
        }

        return true;
    }

    /**
     * @param Customer $customer
     * @param int $illustrationId
     * @return bool
     * @throws DomainException
     * @Annotation return true if is new download
     */
    public function trackDownload(Customer $customer, int $illustrationId): bool
    {
        $newDownload = $this->customerDownloadFactory->create($illustrationId, $customer);
        return $customer->trackDownload($newDownload);
    }

    /**
     * @param Customer $customer
     * @param int $illustrationId
     * @param \DateTime|null $createdDate
     */
    public function migrateDownload(Customer $customer, int $illustrationId, ?\DateTime $createdDate = null): void
    {
        $newDownload = $this->customerDownloadFactory->create($illustrationId, $customer, $createdDate);
        $customer->migrateDownload($newDownload);
    }
}