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

    /** @var MembershipFactory */
    private MembershipFactory $membershipFactory;

    public function __construct(
        SubscriptionFactory $subscriptionFactory,
        SpecificationInterface $isCustomerCanChangeSubscriptionPlanSpecification,
        SpecificationInterface $isModeratorCanChangeSubscriptionPlanSpecification,
        MembershipFactory $membershipFactory
    )
    {
        $this->subscriptionFactory = $subscriptionFactory;
        $this->isCustomerCanChangeSubscriptionPlanSpecification = $isCustomerCanChangeSubscriptionPlanSpecification;
        $this->isModeratorCanChangeSubscriptionPlanSpecification = $isModeratorCanChangeSubscriptionPlanSpecification;
        $this->membershipFactory = $membershipFactory;
    }

    /**
     * @param SubscriptionPlan $subscriptionPlan
     * @param Customer $customer
     * @param bool $isActorModerator
     * @return Subscription
     * @throws DomainException
     */
    public function signing(SubscriptionPlan $subscriptionPlan, Customer $customer, bool $isActorModerator = false): Subscription
    {
        $planCanBeUsed = $isActorModerator ?
            $this->isModeratorCanChangeSubscriptionPlanSpecification->isSatisfiedBy($subscriptionPlan) :
            $this->isCustomerCanChangeSubscriptionPlanSpecification->isSatisfiedBy($subscriptionPlan);

        if (!$planCanBeUsed) {
            throw new DomainException('This plan can not be used.');
        }

        return $this->subscriptionFactory->buildFromSubscriptionPlan($subscriptionPlan, $customer);
    }

    public function wasPaid(Subscription $subscription, float $amountReceived): void
    {
        /** @todo Нужна защита на случай, когда сумма сщета меньше стоимости подписки */

        /** @todo нужно проверять что у кастомера нет активной подписки */
        if ($subscription->getStatus() === Subscription::STATUS_NEW) {
            $subscription->activate();
        }

        $membership = $this->membershipFactory->build($subscription, $amountReceived);
        $subscription->addMembership($membership);
    }
}