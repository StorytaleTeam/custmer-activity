<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Domain\PersistModel\Order\AbstractOrder;

class OrderHydrator
{
    /** @var OrderPositionHydrator */
    private OrderPositionHydrator $orderPositionHydrator;

    public function __construct(OrderPositionHydrator $orderPositionHydrator)
    {
        $this->orderPositionHydrator = $orderPositionHydrator;
    }

    public function toArray(AbstractOrder $order): array
    {
        $orderPositions = [];
        foreach ($order->getOrderPositions() as $orderPosition) {
            $orderPositions[] = $this->orderPositionHydrator->toArray($orderPosition);
        }

        return [
            'id' => $order->getId(),
            'createdDate' => $order->getCreatedDate(),
            'totalPrice' => $order->getTotalPrice(),
            'orderPositions' => $orderPositions,
        ];
    }
}