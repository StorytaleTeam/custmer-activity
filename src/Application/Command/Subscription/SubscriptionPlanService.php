<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanDTO;
use Storytale\CustomerActivity\Application\DTOValidation;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;

class SubscriptionPlanService
{
    /** @var SubscriptionPlanRepository */
    private SubscriptionPlanRepository $subscriptionPlanRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var SubscriptionPlanFactory */
    private SubscriptionPlanFactory $subscriptionPlanFactory;

    /** @var DTOValidation */
    private DTOValidation $subscriptionPlanDTOValidation;

    public function __construct(
        SubscriptionPlanRepository $subscriptionPlanRepository,
        DomainSession $domainSession,
        SubscriptionPlanFactory $subscriptionPlanFactory,
        DTOValidation $subscriptionPlanDTOValidation
    )
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionPlanFactory = $subscriptionPlanFactory;
        $this->subscriptionPlanDTOValidation = $subscriptionPlanDTOValidation;
    }

    public function create(SubscriptionPlanDTO $subscriptionPlanDTO): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->subscriptionPlanDTOValidation->validate($subscriptionPlanDTO);

            $subscriptionPlan = $this->subscriptionPlanFactory->buildFromDTO($subscriptionPlanDTO);
            if(!$subscriptionPlan instanceof SubscriptionPlan) {
                throw new ApplicationException('Error creating SubscriptionPlan');
            }

            $this->subscriptionPlanRepository->save($subscriptionPlan);
            $this->domainSession->flush();

            $result['subscriptionPlan']['id'] = $subscriptionPlan->getId();
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }



}