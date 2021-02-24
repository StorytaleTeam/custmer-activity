<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionPlanDataProvider;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class SubscriptionPlanCustomerController extends AbstractRestfulController
{
    /** @var SubscriptionPlanDataProvider */
    private SubscriptionPlanDataProvider $subscriptionPlanDataProvider;

    public function __construct(SubscriptionPlanDataProvider $subscriptionPlanDataProvider)
    {
        $this->subscriptionPlanDataProvider = $subscriptionPlanDataProvider;
    }

    public function getList()
    {
        $subscriptionPlans = $this->subscriptionPlanDataProvider->findListForCustomer();
        $response = [
            'success' => true,
            'result' => ['subscriptionPlans' => $subscriptionPlans],
        ];

        return new JsonModel($response);
    }
}