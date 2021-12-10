<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Illustration\Illustration;
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
     * @param Illustration $illustration
     * @return bool
     * @throws DomainException
     */
    public function getDownloadPass(Customer $customer, Illustration $illustration): bool
    {
        if ($illustration->isFree() == false) {
            $actualSubscribe = $customer->getActualSubscription();
            if (!$actualSubscribe instanceof Subscription) {
                throw new DomainException('You have not actual subscription.', 109001001);
            }
            $currentMembership = $actualSubscribe->getCurrentMembership();
            if (!$currentMembership instanceof Membership
                || !in_array($currentMembership->getStatus(), [Membership::STATUS_ACTIVE, Membership::STATUS_SPENT_LIMIT])
            ) {
                throw new DomainException("You don't have an active membership.", 109001002);
            }

            $isAlreadyDownloaded = $customer->isAlreadyDownloaded($illustration);
            if (!$isAlreadyDownloaded) {
                if ($currentMembership->getDownloadRemaining() < 1) {
                    throw new DomainException('Downloads limit reached.', 109001003);
                }
            }
        }

        return true;
    }

    /**
     * @param Customer $customer
     * @param Illustration $illustration
     * @return bool
     * @throws DomainException
     * @Annotation return true if is new download
     */
    public function trackDownload(Customer $customer, Illustration $illustration): bool
    {
        $newDownload = $this->customerDownloadFactory->create($illustration, $customer);
        return $customer->trackDownload($newDownload);
    }
}