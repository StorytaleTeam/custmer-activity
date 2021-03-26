<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription;

use Storytale\Contracts\Persistence\DomainSession;
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

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        parent::__construct('subscription:prolongate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subscriptions = $this->subscriptionRepository->getForProlongate(10);
        $output->write('Find ' . count($subscriptions) . " subscriptions for prolongation. \n");

        foreach ($subscriptions as $subscription) {
            $subscription->expireMembership();
        }

        $this->domainSession->flush();
    }
}