<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Query\Order\OrderDataProvider;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class OrderCustomerController extends AbstractActionController
{
    /** @var OrderDataProvider */
    private OrderDataProvider $orderDataProvider;

    public function __construct(OrderDataProvider $orderDataProvider)
    {
        $this->orderDataProvider = $orderDataProvider;
    }

    public function listAction()
    {
        $customerId = $this->params()->fromQuery('customerId');
        if ($customerId !== null) {
            $orders = $this->orderDataProvider->findListForCustomer($customerId);
            $response = [
                'success' => true,
                'result' => [
                    'orders' => $orders,
                ],
            ];
        } else {
            $response = ['success' => false, 'message' => 'Need not empty customerId param.'];
        }

        return new JsonModel($response);
    }

    public function oneAction()
    {
        $customerId = $this->params()->fromQuery('customerId');
        $orderId = $this->params()->fromQuery('orderId');
         if ($customerId !== null && $orderId !== null) {
            $orders = $this->orderDataProvider->findOneForCustomer($customerId, $orderId);
            $response = [
                'success' => true,
                'result' => [
                    'order' => $orders,
                ],
            ];
        } else {
             switch (null) {
                 case $customerId:
                    $response = ['success' => false, 'message' => 'Need not empty customerId param.'];
                    break;
                 case $orderId:
                    $response = ['success' => false, 'message' => 'Need not empty orderId param.'];
                    break;
             }
        }

        return new JsonModel($response);
    }
}