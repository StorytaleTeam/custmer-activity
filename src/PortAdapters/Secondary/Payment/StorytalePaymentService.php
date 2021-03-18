<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Payment;

use Storytale\Contracts\ServiceClient\PaymentClient;
use Storytale\CustomerActivity\Application\ApplicationException;

class StorytalePaymentService implements PaymentService
{
    /** @var PaymentClient */
    private PaymentClient $paymentClient;

    public function __construct(PaymentClient $paymentClient)
    {
        $this->paymentClient = $paymentClient;
    }

    public function getPaymentLink(array $params): string
    {
        $paymentResponse = $this->paymentClient->getPaymentLink($params);
        $paymentLink = null;
        if (isset($paymentResponse['success']) && $paymentResponse['success'] === true) {
            $paymentLink = $paymentResponse['result']['paymentLink'];
        }
        if (empty($paymentLink)) {
            $message = 'Could not create payment link.';
            if (isset($paymentResponse['message' ])) {
                $message .= ' ' . $paymentResponse['message'];
            }
            throw new ApplicationException($message);
        }

        return $paymentLink;
    }
}