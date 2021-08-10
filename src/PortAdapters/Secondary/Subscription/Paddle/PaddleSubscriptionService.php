<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Paddle;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\ServiceClient\PaddleClient;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanHydrator;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\PortAdapters\Secondary\ServiceClient\CommandValidationException;
use Storytale\PortAdapters\Secondary\ServiceClient\Paddle\Command\PaddleCancelSubscriptionCommand;
use Storytale\PortAdapters\Secondary\ServiceClient\Paddle\Command\PaddleCreateSubscriptionPlanCommand;

class PaddleSubscriptionService
{
    /** @var PaddleClient */
    private PaddleClient $paddleClient;

    /** @var SubscriptionPlanHydrator */
    private SubscriptionPlanHydrator $subscriptionPlanHydrator;

    /** @var DomainSession */
    private DomainSession $domainSession;

    public function __construct(
        PaddleClient $paddleClient,
        SubscriptionPlanHydrator $subscriptionPlanHydrator,
        DomainSession $domainSession
    )
    {
        $this->paddleClient = $paddleClient;
        $this->subscriptionPlanHydrator = $subscriptionPlanHydrator;
        $this->domainSession = $domainSession;
    }

    /**
     * @param int $paddleSubscriptionId
     * @throws ValidationException
     */
    public function cancelSubscription(int $paddleSubscriptionId): void
    {
        try {
            $paddleCancelSubscriptionCommand = new PaddleCancelSubscriptionCommand($this->paddleClient, ['subscription_id' => $paddleSubscriptionId]);
            $response = $paddleCancelSubscriptionCommand->run();
            /** @todo нужно удостовериться что падл обработал запрос */
        }  catch (CommandValidationException $e) {
            throw new ValidationException($e->getMessage());
        }
    }

    /**
     * @param SubscriptionPlan $subscriptionPlan
     * @throws ApplicationException
     * @throws \Storytale\CustomerActivity\Domain\DomainException
     */
    public function createSubscriptionPlan(SubscriptionPlan $subscriptionPlan): void
    {
        $subscriptionPlanData = $this->subscriptionPlanHydrator->toArray($subscriptionPlan);
        $paddleCreateSubscriptionPlanCommand = new PaddleCreateSubscriptionPlanCommand($this->paddleClient, $subscriptionPlanData);
        $paddleResponse = $paddleCreateSubscriptionPlanCommand->run();
        if (isset($paddleResponse['success']) && $paddleResponse['success'] === true) {
            if (isset($paddleResponse['response']['product_id'])) {
                $subscriptionPlan->initPaddleId($paddleResponse['response']['product_id']);

                /** @todo не уверен что фалг должен быть на этом уровне */
                $this->domainSession->flush();
            }
        } else {
            throw new ApplicationException('Failure request to Paddle. Create Subscription plan id:' . $subscriptionPlan['id'] ?? null);
        }
    }
}