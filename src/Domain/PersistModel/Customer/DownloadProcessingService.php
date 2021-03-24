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
     */
    public function getDownloadPass(Customer $customer, int $illustrationId): bool
    {
        $hasUnusedDownloads = false;
        $isAlreadyDownloaded = false;

        $actualSubscribe = $customer->getActualSubscription();
        $currentMembership = $actualSubscribe instanceof Subscription ? $actualSubscribe->getCurrentMembership() : null;
        if ($currentMembership instanceof Membership) {
            $isAlreadyDownloaded = $customer->isAlreadyDownloaded($illustrationId);
            if (!$isAlreadyDownloaded) {
                if ($currentMembership->getDownloadRemaining() > 0) {
                    $hasUnusedDownloads = true;
                }
            }
        }

        return $isAlreadyDownloaded || $hasUnusedDownloads;
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
}