<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionSigningDTO;
use Storytale\CustomerActivity\Application\Command\Subscription\SubscriptionService;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionDataProvider;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class SubscriptionController extends AbstractRestfulController
{
    /** @var SubscriptionDataProvider */
    private SubscriptionDataProvider $subscriptionDataProvider;

    /** @var SubscriptionService */
    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionDataProvider $subscriptionDataProvider, SubscriptionService $subscriptionService)
    {
        $this->subscriptionDataProvider = $subscriptionDataProvider;
        $this->subscriptionService = $subscriptionService;
    }

    public function create($data)
    {
        $subscriptionSigningDTO = new SubscriptionSigningDTO($data);
        $response = $this->subscriptionService->signing($subscriptionSigningDTO, true);

        return new JsonModel($response->jsonSerialize());
    }

    public function getList()
    {
        $page = $this->params()->fromQuery('page', 1);
        $count = $this->params()->fromQuery('count', 50);
        $params = $this->params()->fromQuery(null, []);

        $subscriptions = $this->subscriptionDataProvider->findList($count ,$page, $params);
        $response = [
            'success' => true,
            'subscriptions' => $subscriptions,
        ];

        return new JsonModel($response);
    }
}