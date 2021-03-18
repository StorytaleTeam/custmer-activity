<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Payment;

use Storytale\CustomerActivity\Application\ApplicationException;

interface PaymentService
{
    /**
     * @param array $params
     * @return string
     * @throws ApplicationExceptionw
     */
    public function getPaymentLink(array $params): string;
}