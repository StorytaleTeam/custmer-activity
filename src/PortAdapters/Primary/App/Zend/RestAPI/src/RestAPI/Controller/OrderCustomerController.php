<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Order\DTO\ConfirmOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\OrderService;
use Storytale\CustomerActivity\Application\Query\Order\OrderDataProvider;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class OrderCustomerController extends AbstractActionController
{
    /** @var OrderDataProvider */
    private OrderDataProvider $orderDataProvider;

    /** @var OrderService */
    private OrderService $orderService;

    public function __construct(OrderDataProvider $orderDataProvider, OrderService $orderService)
    {
        $this->orderDataProvider = $orderDataProvider;
        $this->orderService = $orderService;
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
        $data = $this->params()->fromQuery(null, []);
        $confirmOrderDTO = new ConfirmOrderDTO($data);
        $response = $this->orderService->getOne($confirmOrderDTO);

        return new JsonModel($response->jsonSerialize());
    }
}