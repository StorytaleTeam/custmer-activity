<?php

namespace Storytale\SkeletonDev\Application\Command\TestOrder;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\SkeletonDev\Application\ApplicationException;
use Storytale\SkeletonDev\Application\OperationResponse;
use Storytale\SkeletonDev\Domain\PersistModel\Order\Order;
use Storytale\SkeletonDev\Domain\PersistModel\Order\OrderRepository;

class TestOrderService
{
    /** @var OrderRepository */
    private OrderRepository $orderRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /**
     * TestOrderService constructor.
     * @param OrderRepository $orderRepository
     * @param DomainSession $domainSession
     */
    public function __construct(OrderRepository $orderRepository, DomainSession $domainSession)
    {
        $this->orderRepository = $orderRepository;
        $this->domainSession = $domainSession;
    }

    /**
     * @return OperationResponse
     */
    public function createOrder()
    {
        $success = false;
        $result = null;
        $message = null;

        try {
            $order = new Order(1, 1, new \DateTime());
            $this->orderRepository->save($order);
            $this->domainSession->flush();
            $success = true;
            $result['orderId'] = $order->getId();
        } catch (ApplicationException $e) {
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }

    /**
     * @param int $id
     * @return OperationResponse
     */
    public function orderWasSold (int $id)
    {
        $success = false;
        $result = null;
        $message = null;

        try {
            $order = $this->orderRepository->findById($id);
            if ($order instanceof Order) {
                $order->wasSold();
                $this->domainSession->flush();
                $success = true;
                $result['orderId'] = $order->getId();
            } else {
                throw new ApplicationException("Order with id:$id not found");
            }
        } catch (ApplicationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }
}