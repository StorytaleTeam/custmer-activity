<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Customer\LikeService;
use Storytale\CustomerActivity\Application\Query\Customer\CustomerDataProvider;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class LikeCustomerController extends AbstractRestfulController
{
    /** @var LikeService */
    private LikeService $likeService;

    /** @var CustomerDataProvider */
    private CustomerDataProvider $customerDataProvider;

    public function __construct(LikeService $likeService, CustomerDataProvider $customerDataProvider)
    {
        $this->likeService = $likeService;
        $this->customerDataProvider = $customerDataProvider;
    }

    public function create($data)
    {
        $customerId = $data['customerId'] ?? null;
        $illustrationId = $data['illustrationId'] ?? null;
        if (empty($customerId)) {
            return new JsonModel(['success' => false, 'message' => 'Need not empty customerId.']);
        }
        if (empty($illustrationId)) {
            return new JsonModel(['success' => false, 'message' => 'Need not empty illustrationId.']);
        }

        $response = $this->likeService->likeAction($customerId, $illustrationId);

        return new JsonModel($response->jsonSerialize());
    }

    public function getList()
    {
        $customerId = $this->params()->fromQuery('customerId');
        if (empty($customerId)) {
            return new JsonModel(['success' => false, 'message' => 'Need not empty customerId.']);
        }
        $likes = $this->customerDataProvider->findCustomerLikes($customerId);
        $response = [
            'success' => true,
            'result' => [
                'likes' => $likes,
            ],
        ];

        return new JsonModel($response);
    }
}