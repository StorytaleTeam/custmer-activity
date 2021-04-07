<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\ExternalEvent;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\EventBus\ExternalEvent;
use Storytale\Contracts\EventBus\ExternalEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Payment\InvoiceWasAuthorizedEvent;
use Storytale\Contracts\SharedEvents\Subscription\Membership\MembershipWasActivatedEvent;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\MembershipDTOAssembler;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionDTOAssembler;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionProcessingService;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;

class OnInvoiceWasAuthorizedHandler implements ExternalEventHandler
{
    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var SubscriptionProcessingService */
    private SubscriptionProcessingService $subscriptionProcessingService;

    /** @var EventBus */
    private EventBus $eventBus;

    /** @var MembershipDTOAssembler */
    private MembershipDTOAssembler $membershipDTOAssembler;

    /** @var SubscriptionDTOAssembler */
    private SubscriptionDTOAssembler $subscriptionDTOAssembler;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        SubscriptionProcessingService $subscriptionProcessingService,
        EventBus $eventBus,
        MembershipDTOAssembler $membershipDTOAssembler,
        SubscriptionDTOAssembler $subscriptionDTOAssembler
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionProcessingService = $subscriptionProcessingService;
        $this->eventBus = $eventBus;
        $this->membershipDTOAssembler = $membershipDTOAssembler;
        $this->subscriptionDTOAssembler = $subscriptionDTOAssembler;
    }

    public function handler(ExternalEvent $event): void
    {
        if ($event instanceof InvoiceWasAuthorizedEvent) {
            $paymentData = $event->getData();
            if (empty($paymentData)) {
                /**
                 * @todo логировать, без остановки скрипта
                 * Get InvoiceWasAuthorizedEvent event with empty data.
                 */
                return;
            }
            if (isset($paymentData['invoice']['subscriptionId'])) {
                $subscription = $this->subscriptionRepository->get($paymentData['invoice']['subscriptionId']);
                if (!$subscription instanceof Subscription) {
                    /**
                     * @todo логировать, без остановки скрипта
                     * Payment received for a non-existent subscription. Unable to process payment.
                     */
                    return;
                }
                $oldMembership = $subscription->getCurrentMembership();
                $oldMembershipId = $oldMembership instanceof Membership ? $oldMembership->getId() : null;
                $this->subscriptionProcessingService->wasPaid($subscription, $paymentData['invoice']['amount']);
                $this->domainSession->flush();

                $newMembership = $subscription->getCurrentMembership();
                if (
                    $newMembership instanceof Membership
                    && $newMembership->getId() !== $oldMembershipId
                    && $newMembership->getStatus() === Membership::STATUS_ACTIVE
                ) {
                    $this->eventBus->fire(new MembershipWasActivatedEvent([
                        'membership' => $this->membershipDTOAssembler->toArray($newMembership),
                        'subscription' => $this->subscriptionDTOAssembler->toArray($subscription),
                    ]));
                }
            } else {
                /** @todo логировать сообщение о пустом параметре */
            }
        }
    }
}