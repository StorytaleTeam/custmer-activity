<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

class CustomerDownloadFactory
{
    public function create(int $illustrationId, Customer $customer, ?\DateTime $createdDate = null): CustomerDownload
    {
        $createdDate = $createdDate ?? new \DateTime();
        return new CustomerDownload($illustrationId, $customer, $createdDate, 0);
    }
}