<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Subscription\Membership\MembershipWasActivatedEvent;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\MembershipDTOAssembler;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionDTOAssembler;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProlongateSubscriptionCommand extends Command
{
    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var EventBus */
    private EventBus $eventBus;

    /** @var MembershipDTOAssembler */
    private MembershipDTOAssembler $membershipDTOAssembler;

    /** @var SubscriptionDTOAssembler */
    private SubscriptionDTOAssembler $subscriptionDTOAssembler;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        EventBus $eventBus,
        MembershipDTOAssembler $membershipDTOAssembler,
        SubscriptionDTOAssembler $subscriptionDTOAssembler
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        $this->eventBus = $eventBus;
        $this->membershipDTOAssembler = $membershipDTOAssembler;
        $this->subscriptionDTOAssembler = $subscriptionDTOAssembler;
        parent::__construct('subscription:prolongate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subscriptions = $this->subscriptionRepository->getForProlongate(10);
        $output->write('Find ' . count($subscriptions) . " subscriptions for prolongation. \n");

        foreach ($subscriptions as $subscription) {
            $oldMembership = $subscription->getCurrentMembership();
            $subscription->expireMembership();
            $this->domainSession->flush();

            $oldMembershipId = $oldMembership instanceof Membership ? $oldMembership->getId() : null;
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
        }

        return 1;
    }
}