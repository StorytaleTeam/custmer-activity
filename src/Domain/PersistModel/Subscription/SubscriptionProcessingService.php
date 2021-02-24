<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Subscription;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\SpecificationInterface;

class SubscriptionProcessingService
{
    /** @var SubscriptionFactory */
    private SubscriptionFactory $subscriptionFactory;

    /** @var SpecificationInterface */
    private SpecificationInterface $isCustomerCanChangeSubscriptionPlanSpecification;

    /** @var SpecificationInterface */
    private SpecificationInterface $isModeratorCanChangeSubscriptionPlanSpecification;

    public function __construct(
        SubscriptionFactory $subscriptionFactory,
        SpecificationInterface $isCustomerCanChangeSubscriptionPlanSpecification,
        SpecificationInterface $isModeratorCanChangeSubscriptionPlanSpecification
    )
    {
        $this->subscriptionFactory = $subscriptionFactory;
        $this->isCustomerCanChangeSubscriptionPlanSpecification = $isCustomerCanChangeSubscriptionPlanSpecification;
        $this->isModeratorCanChangeSubscriptionPlanSpecification = $isModeratorCanChangeSubscriptionPlanSpecification;
    }

    public function prolongation(Customer $customer)
    {

    }

    /**
     * @param SubscriptionPlan $subscriptionPlan
     * @param Customer $customer
     * @param bool $isActorModerator
     * @return Subscription
     * @throws DomainException
     */
    public function firstSigning(SubscriptionPlan $subscriptionPlan, Customer $customer, bool $isActorModerator = false): Subscription
    {
        $planCanBeUsed = $isActorModerator ?
            $this->isModeratorCanChangeSubscriptionPlanSpecification->isSatisfiedBy($subscriptionPlan) :
            $this->isCustomerCanChangeSubscriptionPlanSpecification->isSatisfiedBy($subscriptionPlan);

        if (!$planCanBeUsed) {
            throw new DomainException('This plan can not be used.');
        }

        return $this->subscriptionFactory->buildFromSubscriptionPlan($subscriptionPlan, $customer);
    }

    public function changePlan(Customer $customer, SubscriptionPlan $subscriptionPlan)
    {

    }

    public function earlyProlongation(Customer $customer)
    {

    }
}