<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Query\Subscription\OldSubscriptionDataProvider;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\MembershipFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\MembershipRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;
use Storytale\PortAdapters\Secondary\Console\AbstractMigrateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateOldMembershipsCommand
 * @package Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription
 * @deprecated
 */
class MigrateOldMembershipsCommand extends AbstractMigrateCommand
{
    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var OldSubscriptionDataProvider */
    private OldSubscriptionDataProvider $oldSubscriptionDataProvider;

    /** @var MembershipFactory */
    private MembershipFactory $membershipFactory;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var MembershipRepository */
    private MembershipRepository $membershipRepository;


    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        OldSubscriptionDataProvider $oldSubscriptionDataProvider,
        MembershipFactory $membershipFactory,
        DomainSession $domainSession,
        MembershipRepository $membershipRepository
    )
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->oldSubscriptionDataProvider = $oldSubscriptionDataProvider;
        $this->membershipFactory = $membershipFactory;
        $this->domainSession = $domainSession;
        $this->membershipRepository = $membershipRepository;
        parent::__construct('old:migrateMemberships');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start($input, $output, 100000, false);

        $count = 50;
        $page = 1;
        while (true) {
            $oldMemberships = $this->oldSubscriptionDataProvider->getMemberships($count, $page);
            $page++;
            if (count($oldMemberships) < 1) {
                break;
            }
            foreach ($oldMemberships as $oldMembership) {
                $oldMembershipId = $oldMembership['ID'];
                $membershipMetas = $this->oldSubscriptionDataProvider->getMetaForPost($oldMembershipId);

                $createdDate = null;
                if (isset($oldMembership['post_date'])) {
                    try {
                        $createdDate = new \DateTime($oldMembership['post_date']);
                    } catch (\Exception $e) {
                        $createdDate = null;
                    }
                }

                $oldSubscriptionId = null;
                $downloadLimit = null;
                $newStatus = null;
                $startDate = null;
                $oldCustomerId = null;

                try {
                     $this->parsMeta($membershipMetas, $oldSubscriptionId, $downloadLimit, $newStatus, $startDate, $oldCustomerId);
                } catch (ValidationException $e) {
                    $this->registerError($e->getMessage() . ". OldMembershipID: $oldMembershipId");
                    continue;
                } catch (DomainException $e) {
                    $this->registerStatus($e->getMessage());
                    continue;
                }

                $subscription = $this->subscriptionRepository->getByOldId($oldSubscriptionId);
                if (!$subscription instanceof Subscription) {
                    continue;
                }
                if ($subscription->getStatus() == Subscription::STATUS_STOPPED) {
                    continue;
                }
                if ($subscription->getCustomer()->getOldId() !== $oldCustomerId) {
                    $this->registerError("Dismiss customerId. OldMembershipID: $oldMembershipId");
                    continue;
                }

                $membership = $this->membershipFactory->buildAllFields(
                    $subscription, 0, $newStatus,
                    $downloadLimit, null, $oldMembershipId,
                    $createdDate
                );
//                $subscription->addMembership($membership);
                $this->membershipRepository->save($membership);
                $this->domainSession->flush();
                $this->successSave();
            }
            $this->domainSession->close();
        }
        $this->finish();
    }

    private function parsMeta(
        array $subscriptionMetas, &$oldSubscriptionId, &$downloadLimit,
        &$newStatus, &$startDate, &$oldCustomerId
    )
    {
        foreach ($subscriptionMetas as $subscriptionMeta) {
            switch ($subscriptionMeta['meta_key']) {
                case '_title':
                    if ($subscriptionMeta['meta_value'] === 'Free Plan') {
                        throw new DomainException('fre plan');
                    }
                    break;
                case '_status':
                    switch ($subscriptionMeta['meta_value']) {
//                        resumed, expiring, not_active
                        case 'active':
                            $newStatus = Membership::STATUS_PAID;
                            break;
                        case 'expired':
                            $newStatus = Membership::STATUS_DURATION_EXPIRED;
                            break;
                        case 'cancelled':
                            $newStatus = Membership::STATUS_CANCELED_BY_ADMIN;
                            break;
                    }
                    break;
                case '_subscription_id':
                    $oldSubscriptionId = $subscriptionMeta['meta_value'];
                    break;
                case '_credits':
                    $downloadLimit = $subscriptionMeta['meta_value'];
                    break;
                case '_start_date':
                    $startDate = (new \DateTime)->setTimestamp($subscriptionMeta['meta_value']);
                    break;
                case '_user_id':
                    $oldCustomerId = (int)$subscriptionMeta['meta_value'];
                    break;
//                case '':
//                    $ = $subscriptionMeta['meta_value'];
//                    break;
            }
        }

        if ($newStatus === null) {
            throw new ValidationException('Get membership with empty status');
        }
        if ($oldSubscriptionId === null) {
            throw new ValidationException('Get membership with empty odlSubscriptionId');
        }
        if ($downloadLimit === null) {
            throw new ValidationException('Get membership with empty downloadLimit');
        }
        if ($oldCustomerId === null) {
            throw new ValidationException('Get membership with empty oldCustomerId');
        }
//        if ( === null) {
//            throw new ValidationException('Get order with empty ');
//        }
    }
}