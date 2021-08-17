<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Query\Subscription\OldSubscriptionDataProvider;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;
use Storytale\PortAdapters\Secondary\Console\AbstractMigrateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateOldSubscriptionCommand
 * @package Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription
 * @deprecated
 */
class MigrateOldSubscriptionCommand extends AbstractMigrateCommand
{
    /** @var OrderRepository */
    private OrderRepository $orderRepository;

    /** @var SubscriptionRepository */
    private SubscriptionRepository $subscriptionRepository;

    /** @var OldSubscriptionDataProvider */
    private OldSubscriptionDataProvider $oldSubscriptionDataProvider;

    /** @var SubscriptionFactory */
    private SubscriptionFactory $subscriptionFactory;

    /** @var DomainSession */
    private DomainSession $domainSession;

    public function __construct(
        OrderRepository $orderRepository,
        SubscriptionRepository $subscriptionRepository,
        OldSubscriptionDataProvider $oldSubscriptionDataProvider,
        SubscriptionFactory $subscriptionFactory,
        DomainSession $domainSession
    )
    {
        $this->orderRepository = $orderRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->oldSubscriptionDataProvider = $oldSubscriptionDataProvider;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->domainSession = $domainSession;
        parent::__construct('old:migrateSubscriptions');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start($input, $output, 1200, false);

        $count = 50;
        $page = 1;
        while (true) {
            $oldSubscriptions = $this->oldSubscriptionDataProvider->getSubscriptions($count, $page);
            $page++;
            if (count($oldSubscriptions) < 1) {
                break;
            }
            foreach ($oldSubscriptions as $oldSubscription) {
                $oldSubscriptionId = $oldSubscription['ID'];
                $sameSubscription = $this->subscriptionRepository->getByOldId($oldSubscriptionId);
                if ($sameSubscription instanceof Subscription) {
                    $this->alreadyExist();
                    continue;
                }

                $newStatus = null;
                $autoRenewal = null;
                $startDate = null;
                $endDate = null;
                $oldProductId = null;
                $oldOrderId = null;
                $oldUserId = null;
                $membershipCycle = 0;
                /** @todo как правильно определить paddleId? */
                $paddleId = 0;

                $createdDate = null;
                if (isset($oldSubscription['post_date'])) {
                    try {
                        $createdDate = new \DateTime($oldSubscription['post_date']);
                    } catch (\Exception $e) {
                        $createdDate = null;
                    }
                }

                $subscriptionMetas = $this->oldSubscriptionDataProvider->getMetaForPost($oldSubscription['ID']);
                try {
                    $this->parsMeta($subscriptionMetas, $newStatus, $autoRenewal, $startDate, $endDate, $oldProductId, $oldOrderId, $oldUserId);
                } catch (ValidationException $e) {
                    $this->registerError($e->getMessage() . ". OldSubscriptionId: $oldSubscriptionId");
                }

                $order = $this->orderRepository->getByOldId($oldOrderId);
                if (!$order instanceof OrderSubscription) {
                    if ($newStatus === Subscription::STATUS_ACTIVE) {
                        $this->registerError("Order with id $oldOrderId not found for subscription $oldSubscriptionId");
                    }
                    continue;
                }
                $subscription = $this->subscriptionFactory->buildAllFields(
                    $order->getCustomer(),
                    $order->getSubscriptionPlan(),
                    $newStatus,
                    $membershipCycle,
                    $autoRenewal,
                    $paddleId,
                    $oldSubscriptionId,
                    $createdDate
                );
                $order->assignSubscription($subscription);

                $this->subscriptionRepository->save($subscription);
                $this->domainSession->flush();
                $this->successSave();
            }
            $this->domainSession->close();
        }
        $this->finish();
    }

    private function parsMeta(
        array $subscriptionMetas, &$newStatus, &$autoRenewal,
        &$startDate, &$endDate, &$oldProductId,
        &$oldOrderId, &$oldUserId
    )
    {
        foreach ($subscriptionMetas as $subscriptionMeta) {
            switch ($subscriptionMeta['meta_key']) {
                case 'status':
                    switch ($subscriptionMeta['meta_value']) {
                        case 'active':
                            $newStatus = Subscription::STATUS_ACTIVE;
                            $autoRenewal = true;
                            break;
                        case 'pending':
                            $newStatus = Subscription::STATUS_NEW;
                            $autoRenewal = true;
                            break;
                        case 'overdue':
                        case 'cancelled':
                            $newStatus = Subscription::STATUS_STOPPED;
                            $autoRenewal = false;
                            break;
                    }
                    break;
                case 'start_date':
                    $startDate = (new \DateTime)->setTimestamp($subscriptionMeta['meta_value']);
                    break;
                case 'cancelled_date':
                case 'end_date':
                    if ($endDate === null) {
                        $endDate = (new \DateTime)->setTimestamp($subscriptionMeta['meta_value']);
                    }
                    break;
                case 'product_id':
                    $oldProductId = $subscriptionMeta['meta_value'];
                    break;
//                case 'order_id':
//                    $oldOrderId = $subscriptionMeta['meta_value'];
//                    break;
                case 'order_ids':
                    $oldOrderIds = $subscriptionMeta['meta_value'];
                    $oldOrderIds = unserialize($oldOrderIds);
                    if (is_array($oldOrderIds) && count($oldOrderIds) > 0) {
                        $oldOrderId = $oldOrderIds[array_key_last($oldOrderIds)];
                    }
                    break;
                case 'user_id':
                    $oldUserId = $subscriptionMeta['meta_value'];
                    break;
            }
        }

        if ($newStatus === null) {
            throw new ValidationException('Get order with empty status');
        }
        if ($oldProductId === null) {
            throw new ValidationException('Get order with empty oldProductId');
        }
        if ($oldOrderId === null) {
            throw new ValidationException('Get order with empty oldOrderId');
        }
        if ($oldUserId === null) {
            throw new ValidationException('Get order with empty oldUserId');
        }
    }
}