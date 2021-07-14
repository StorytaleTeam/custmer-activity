<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Order\DTO\CreateOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\OrderService;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class OrderController extends AbstractRestfulController
{
    /** @var OrderService */
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function create($data)
    {
        $createOrderDTO = new CreateOrderDTO($data);
        $response = $this->orderService->create($createOrderDTO);

        return new JsonModel($response->jsonSerialize());
    }
}