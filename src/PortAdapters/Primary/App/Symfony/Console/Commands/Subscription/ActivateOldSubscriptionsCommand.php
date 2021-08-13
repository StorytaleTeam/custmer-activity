<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderSubscriptionRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\MembershipFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\MembershipRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;
use Storytale\PortAdapters\Secondary\Console\AbstractMigrateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ActivateOldSubscriptionsCommand
 * @package Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription
 * @deprecated
 */
class ActivateOldSubscriptionsCommand extends AbstractMigrateCommand
{
    private const OLD_YEAR_PLANS = [4621, 4622, 13384, 13387];

    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var OrderSubscriptionRepository  */
    private OrderSubscriptionRepository $orderSubscriptionRepository;

    /** @var MembershipFactory */
    private MembershipFactory $membershipFactory;

    /** @var MembershipRepository */
    private MembershipRepository $membershipRepository;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        DomainSession $domainSession,
        OrderSubscriptionRepository $orderSubscriptionRepository,
        MembershipFactory $membershipFactory,
        MembershipRepository $membershipRepository
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->domainSession = $domainSession;
        $this->orderSubscriptionRepository = $orderSubscriptionRepository;
        $this->membershipFactory = $membershipFactory;
        $this->membershipRepository = $membershipRepository;
        parent::__construct('old:activateOldSubscription');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $subscriptions = $this->subscriptionRepository->getOldForActivate();
        $this->start($input, $output, count($subscriptions), false);

        foreach ($subscriptions as $subscription) {
            $subscription->expireMembership();

            if (in_array($subscription->getSubscriptionPlan()->getOldId(), self::OLD_YEAR_PLANS )) {
                $this->addMembershipForYear($subscription);
            }

            $this->domainSession->flush();
            $this->successSave();
        }
        $this->domainSession->close();
        $this->finish();
    }

    public function addMembershipForYear(Subscription $subscription)
    {
        $order = $this->orderSubscriptionRepository->getBySubscription($subscription);
        $startDate = $order->getCreatedDate();
        $endDate = clone $startDate;
        $endDate->modify('+1 year');
        $nowDate = new \DateTime();
        if ($nowDate < $endDate) {
            $interval = $nowDate->diff($endDate);
            $neededMembershipCount = $interval->m;
            if ($interval->d >= 10) {
                $neededMembershipCount++;
            }
            $newMembershipCount = $neededMembershipCount - $subscription->getMembershipCount();

            for ($i = 1; $i <= $newMembershipCount; $i++) {
                $membership = $this->membershipFactory->buildAllFields(
                    $subscription,
                    0,
                    Membership::STATUS_PAID,
                    $subscription->getSubscriptionPlan()->getDownloadLimit(),
                    null
                );
                $this->membershipRepository->save($membership);
            }
        }
    }
}