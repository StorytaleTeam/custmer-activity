<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Subscription\Membership\MembershipWasActivatedEvent;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\MembershipHydrator;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionHydrator;
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

    /** @var MembershipHydrator */
    private MembershipHydrator $membershipHydrator;

    /** @var SubscriptionHydrator */
    private SubscriptionHydrator $subscriptionHydrator;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        EventBus $eventBus,
        MembershipHydrator $membershipHydrator,
        SubscriptionHydrator $subscriptionHydrator
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        $this->eventBus = $eventBus;
        $this->membershipHydrator = $membershipHydrator;
        $this->subscriptionHydrator = $subscriptionHydrator;
        parent::__construct('subscription:prolongate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write((new \DateTime())->format(\DateTime::ATOM) . ' ');
        $subscriptions = $this->subscriptionRepository->getForProlongate();
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
                    'membership' => $this->membershipHydrator->toArray($newMembership),
                    'subscription' => $this->subscriptionHydrator->toArray($subscription),
                ]));
            }
        }

        return 1;
    }
}