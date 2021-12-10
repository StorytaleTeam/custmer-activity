<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\CustomerActivity\Domain\PersistModel\Illustration\Illustration;

class CustomerDownloadFactory
{
    public function create(Illustration $illustration, Customer $customer, ?\DateTime $createdDate = null): CustomerDownload
    {
        $createdDate = $createdDate ?? new \DateTime();
        return new CustomerDownload($illustration->getId(), $illustration->isFree(), $customer, $createdDate, 0);
    }
}