<?php

namespace RestAPI\Controller;


use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionPlanDTO;
use Storytale\CustomerActivity\Application\Command\Subscription\SubscriptionPlanService;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionPlanDataProvider;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class SubscriptionPlanController extends AbstractRestfulController
{
    /** @var SubscriptionPlanDataProvider */
    private SubscriptionPlanDataProvider $subscriptionPlanDataProvider;

    /** @var SubscriptionPlanService */
    private SubscriptionPlanService $subscriptionPlanService;

    public function __construct(
        SubscriptionPlanDataProvider $subscriptionPlanDataProvider,
        SubscriptionPlanService $subscriptionPlanService
    )
    {
        $this->subscriptionPlanDataProvider = $subscriptionPlanDataProvider;
        $this->subscriptionPlanService = $subscriptionPlanService;
    }

    public function create($data)
    {
        $subscriptionPlanDTO = new SubscriptionPlanDTO($data);
        $response = $this->subscriptionPlanService->create($subscriptionPlanDTO);

        return new JsonModel($response->jsonSerialize());
    }

    public function getList()
    {
        $subscriptionPlans = $this->subscriptionPlanDataProvider->findListForAdmin();
        $response = [
            'success' => true,
            'result' => ['subscriptionPlans' => $subscriptionPlans],
        ];

        return new JsonModel($response);
    }
}