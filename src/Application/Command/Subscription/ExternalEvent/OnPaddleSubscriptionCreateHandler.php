<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\ExternalEvent;

use Storytale\Contracts\EventBus\ExternalEvent;
use Storytale\Contracts\EventBus\ExternalEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Payment\Paddle\GeneralizedPaddleEvent;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;

class OnPaddleSubscriptionCreateHandler implements ExternalEventHandler
{
    private const EVENT_NAME_PADDLE_CREATE_SUBSCRIPTION = 'subscription_created';

    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    public function __construct(SubscriptionRepository $subscriptionRepository, DomainSession $domainSession)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
    }

    public function handler(ExternalEvent $event): void
    {
        if ($event instanceof GeneralizedPaddleEvent) {
            $alertName = $event->getPaddleData()['alert_name'] ?? null;
            if ($alertName === self::EVENT_NAME_PADDLE_CREATE_SUBSCRIPTION) {
                $subscriptionId = $event->getStorytaleData()['subscriptionId'] ?? null;
                if (empty($subscriptionId)) {
                    throw new ApplicationException('Get paddle event with empty subscriptionId param.');
                }
                $subscription = $this->subscriptionRepository->get($subscriptionId);
                if (!$subscription instanceof Subscription) {
                    throw new ApplicationException('Get paddle event for not existing subscription.');
                }

                $paddleSubscriptionId = $event->getPaddleData()['subscription_id'] ?? null;
                $subscription->initPaddleId($paddleSubscriptionId);
                $this->domainSession->flush();
            }
        }
    }
}