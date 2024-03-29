<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription;

use Storytale\Contracts\Domain\ICompositeDomainEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanDTO;
use Storytale\CustomerActivity\Application\DTOValidation;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Specification\IsSubscriptionPlanCanMoveToStatusSpecification;
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

    /** @var IsSubscriptionPlanCanMoveToStatusSpecification */
    private IsSubscriptionPlanCanMoveToStatusSpecification $isSubscriptionPlanCanMoveToStatusSpecification;

    /** @var ICompositeDomainEventHandler */
    private ICompositeDomainEventHandler $compositeDomainEventHandler;

    public function __construct(
        SubscriptionPlanRepository $subscriptionPlanRepository,
        DomainSession $domainSession,
        SubscriptionPlanFactory $subscriptionPlanFactory,
        DTOValidation $subscriptionPlanDTOValidation,
        IsSubscriptionPlanCanMoveToStatusSpecification $isSubscriptionPlanCanMoveToStatusSpecification,
        ICompositeDomainEventHandler $compositeDomainEventHandler
    )
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->domainSession = $domainSession;
        $this->subscriptionPlanFactory = $subscriptionPlanFactory;
        $this->subscriptionPlanDTOValidation = $subscriptionPlanDTOValidation;
        $this->isSubscriptionPlanCanMoveToStatusSpecification = $isSubscriptionPlanCanMoveToStatusSpecification;
        $this->compositeDomainEventHandler = $compositeDomainEventHandler;
    }

    public function create(SubscriptionPlanDTO $subscriptionPlanDTO): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->subscriptionPlanDTOValidation->validate($subscriptionPlanDTO);

            $subscriptionPlan = $this->subscriptionPlanFactory->buildFromDTO($subscriptionPlanDTO);
            if(!$subscriptionPlan instanceof SubscriptionPlan) {
                throw new ValidationException('Error creating SubscriptionPlan');
            }
            $this->subscriptionPlanRepository->save($subscriptionPlan);
            $events = $this->domainSession->flush();

            $this->compositeDomainEventHandler->handleArray($events);

            /** @todo этот код не срабатывает :( */
            if (!empty($subscriptionPlanDTO->getStatus())) {
                if ($this->isSubscriptionPlanCanMoveToStatusSpecification->isSatisfiedBy($subscriptionPlan, $subscriptionPlanDTO->getStatus())) {
                    $subscriptionPlan->changeStatus($subscriptionPlanDTO->getStatus());
                }
            }
            $events = $this->domainSession->flush();

            $result['subscriptionPlan']['id'] = $subscriptionPlan->getId();
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }

    public function edit(array $data): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            if (!isset($data['id'])) {
                throw new ValidationException('Need not empty `id` param.');
            }
            if (!isset($data['status'])) {
                throw new ValidationException('Need not empty `status` param.');
            }

            $subscriptionPlan = $this->subscriptionPlanRepository->get($data['id']);
            if (!$subscriptionPlan instanceof SubscriptionPlan) {
                throw new ValidationException('Plan with this id not found');
            }

            /** @todo edit description */
            
            if ($this->isSubscriptionPlanCanMoveToStatusSpecification->isSatisfiedBy($subscriptionPlan, $data['status'])) {
                $subscriptionPlan->changeStatus($data['status']);
            } else {
                throw new ValidationException('SubscriptionPlan can not be move to this status.');
            }

            $this->domainSession->flush();
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }

}