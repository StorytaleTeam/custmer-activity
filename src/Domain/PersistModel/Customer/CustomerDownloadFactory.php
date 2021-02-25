<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

class CustomerDownloadFactory
{
    public function create(int $illustrationId, Customer $customer): CustomerDownload
    {
        return new CustomerDownload($illustrationId, $customer, new \DateTime(), 0);
    }
}