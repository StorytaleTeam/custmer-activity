<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\SubscriptionPlan;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\ServiceClient\PaddleClient;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanDTOAssembler;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;
use Storytale\PortAdapters\Secondary\ServiceClient\Paddle\Command\PaddleCreateSubscriptionPlanCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncWithPaddleCommand extends Command
{
    /** @var SubscriptionPlanRepository */
    private SubscriptionPlanRepository $subscriptionPlanRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var PaddleClient */
    private PaddleClient $paddleClient;

    /** @var SubscriptionPlanDTOAssembler */
    private SubscriptionPlanDTOAssembler $subscriptionPlanDTOAssembler;

    public function __construct(
        SubscriptionPlanRepository $subscriptionPlanRepository,
        DomainSession $domainSession, PaddleClient $paddleClient,
        SubscriptionPlanDTOAssembler $subscriptionPlanDTOAssembler
    )
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->domainSession = $domainSession;
        $this->paddleClient = $paddleClient;
        $this->subscriptionPlanDTOAssembler = $subscriptionPlanDTOAssembler;
        parent::__construct('subscriptionPlan:sync-with-paddle');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subscriptionPlans = $this->subscriptionPlanRepository->getAll();
        if (count($subscriptionPlans) > 0) {
            foreach ($subscriptionPlans as $subscriptionPlan) {
                if (!empty($subscriptionPlan->getPaddleId())) {
                    continue;
                }
                $subscriptionPlanData = $this->subscriptionPlanDTOAssembler->toArray($subscriptionPlan);
                $paddleCreateSubscriptionPlanCommand = new PaddleCreateSubscriptionPlanCommand($this->paddleClient, $subscriptionPlanData);
                $paddleResponse = $paddleCreateSubscriptionPlanCommand->run();
                if (isset($paddleResponse['success']) && $paddleResponse['success'] === true) {
                    if (isset($paddleResponse['response']['product_id'])) {
                        $subscriptionPlan->initPaddleId($paddleResponse['response']['product_id']);
                        $this->domainSession->flush();
                    }
                } else {
                    throw new ApplicationException('Failure request to Paddle. Create Subscription plan id:' . $subscriptionPlan['id'] ?? null);
                }
            }
        }
    }
}