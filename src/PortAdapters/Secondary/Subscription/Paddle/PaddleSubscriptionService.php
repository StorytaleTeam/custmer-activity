<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Paddle;

use Storytale\Contracts\ServiceClient\PaddleClient;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\PortAdapters\Secondary\ServiceClient\CommandValidationException;
use Storytale\PortAdapters\Secondary\ServiceClient\Paddle\Command\PaddleCancelSubscriptionCommand;

class PaddleSubscriptionService
{
    /** @var PaddleClient */
    private PaddleClient $paddleClient;

    public function __construct(PaddleClient $paddleClient)
    {
        $this->paddleClient = $paddleClient;
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
        }  catch (CommandValidationException $e) {
            throw new ValidationException($e->getMessage());
        }
    }
}