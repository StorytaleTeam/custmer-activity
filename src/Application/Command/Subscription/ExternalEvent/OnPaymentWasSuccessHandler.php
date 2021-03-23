<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\ExternalEvent;

use Storytale\Contracts\EventBus\ExternalEvent;
use Storytale\Contracts\EventBus\ExternalEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionProcessingService;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;

class OnPaymentWasSuccessHandler implements ExternalEventHandler
{
    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var SubscriptionProcessingService */
    private SubscriptionProcessingService $subscriptionProcessingService;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        SubscriptionProcessingService $subscriptionProcessingService
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionProcessingService = $subscriptionProcessingService;
    }

    public function handler(ExternalEvent $event): void
    {
        /** @todo change instance!!!! */
        if ($event instanceof ExternalEvent) {
            $paymentData = $event->jsonSerialize();
            $paymentData = $paymentData['paymentData'] ?? null;
            if (empty($paymentData)) {
                /** @todo логировать, без остановки скрипта */
                throw new ApplicationException('Get event with empty data.');
            }
            if (isset($paymentData['storytale']['subscriptionId'])) {
                $subscription = $this->subscriptionRepository->get($paymentData['storytale']['subscriptionId']);
                if (!$subscription instanceof Subscription) {
                    /** @todo логировать, без остановки скрипта */
                    throw new ValidationException('Payment received for a non-existent subscription. Unable to process payment.');
                }
                $this->subscriptionProcessingService->wasPaid($subscription);
                $this->domainSession->flush();
            } else {
                /** @todo логировать сообщение о пустом параметре */
            }
        }
    }
}