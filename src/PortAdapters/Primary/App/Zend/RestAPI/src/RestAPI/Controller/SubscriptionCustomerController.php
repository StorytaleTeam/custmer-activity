<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Subscription\DTO\SubscriptionSigningDTO;
use Storytale\CustomerActivity\Application\Command\Subscription\SubscriptionService;
use Storytale\CustomerActivity\Application\Query\Subscription\SubscriptionDataProvider;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class SubscriptionCustomerController extends AbstractRestfulController
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

        /** @Annotation The second argument CANNOT be TRUE */
        $response = $this->subscriptionService->signing($subscriptionSigningDTO);

        return new JsonModel($response->jsonSerialize());
    }

    public function getList()
    {
        $page = $this->params()->fromQuery('page', 1);
        $count = $this->params()->fromQuery('count', 50);
        $customerId = $this->params()->fromQuery('customerId');
        if (empty($customerId)) {
            return new JsonModel(['success' => false, 'message' => 'Need not empty `customerId` param.']);
        }

        $subscriptions = $this->subscriptionDataProvider->findAllByCustomer($customerId, $count ,$page);
        $response = [
            'success' => true,
            'result' => [
                'subscriptions' => $subscriptions,
            ],
        ];

        return new JsonModel($response);
    }

    public function delete($id)
    {
        $customerId = $this->params()->fromQuery('customerId');
        if (empty($customerId)) {
            return new JsonModel(['success' => false, 'message' => 'Need not empty `customerId` param.']);
        }

        $response = $this->subscriptionService->unsigning($id, $customerId);

        return new JsonModel($response->jsonSerialize());
    }

    public function get($id)
    {
        $customerId = $this->params()->fromQuery('customerId');
        if (empty($customerId)) {
            return new JsonModel(['success' => false, 'message' => 'Need not empty `customerId` param.']);
        }

        $subscription = $this->subscriptionDataProvider->findOneForCustomer($id, $customerId);

        return new JsonModel([
            'success' => true,
            'result' => [
                'subscription' => $subscription,
            ],
        ]);
    }
}