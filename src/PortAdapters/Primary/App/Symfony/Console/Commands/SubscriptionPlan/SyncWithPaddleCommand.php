<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\SubscriptionPlan;

use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;
use Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Paddle\PaddleSubscriptionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncWithPaddleCommand extends Command
{
    /** @var SubscriptionPlanRepository */
    private SubscriptionPlanRepository $subscriptionPlanRepository;

    /** @var PaddleSubscriptionService */
    private PaddleSubscriptionService $paddleSubscriptionService;

    public function __construct(
        SubscriptionPlanRepository $subscriptionPlanRepository,
        PaddleSubscriptionService $paddleSubscriptionService
    )
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->paddleSubscriptionService = $paddleSubscriptionService;
        parent::__construct('subscriptionPlan:sync-with-paddle');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subscriptionPlans = $this->subscriptionPlanRepository->getAll();
        if (count($subscriptionPlans) > 0) {
            foreach ($subscriptionPlans as $subscriptionPlan) {
                if (empty($subscriptionPlan->getPaddleId())) {
                    $this->paddleSubscriptionService->createSubscriptionPlan($subscriptionPlan);
                }
            }
        }
    }
}