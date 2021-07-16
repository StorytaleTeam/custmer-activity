<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Order\DTO\ConfirmOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\DTO\CreateOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\OrderService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class OrderController extends AbstractActionController
{
    /** @var OrderService */
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createAction()
    {
        $data = $this->params()->fromPost(null, []);
        $createOrderDTO = new CreateOrderDTO($data);
        $response = $this->orderService->create($createOrderDTO);

        return new JsonModel($response->jsonSerialize());
    }

    public function confirmAction()
    {
        $data = $this->params()->fromPost(null, []);
        $confirmOrderDTO = new ConfirmOrderDTO($data);
        $response = $this->orderService->confirm($confirmOrderDTO);

        return new JsonModel($response->jsonSerialize());
    }
}