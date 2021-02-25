<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Query\Customer\CustomerDataProvider;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class CustomerController extends AbstractRestfulController
{
    /** @var CustomerDataProvider */
    private CustomerDataProvider $customerDataProvider;

    public function __construct(CustomerDataProvider $customerDataProvider)
    {
        $this->customerDataProvider = $customerDataProvider;
    }

    public function getList()
    {
        $page = $this->params()->fromQuery('page', 1);
        $count = $this->params()->fromQuery('count', 50);
        $customers = $this->customerDataProvider->findListForAdmin($count, $page);
        $response = ['success' => true, 'result' => ['customers' => $customers]];

        return new JsonModel($response);
    }
}