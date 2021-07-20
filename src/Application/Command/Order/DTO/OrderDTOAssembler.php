<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Domain\PersistModel\Order\Order;

class OrderDTOAssembler
{
    /** @var ProductPositionDTOAssembler */
    private ProductPositionDTOAssembler $productPositionDTOAssembler;

    public function __construct(ProductPositionDTOAssembler $productPositionDTOAssembler)
    {
        $this->productPositionDTOAssembler = $productPositionDTOAssembler;
    }

    public function toArray(Order $order): array
    {
        $productPositions = [];
        foreach ($order->getProductPositions() as $productPosition) {
            $productPositions[] = $this->productPositionDTOAssembler->toArray($productPosition);
        }

        return [
            'id' => $order->getId(),
            'createdDate' => $order->getCreatedDate(),
            'productPositions' => $productPositions,
        ];
    }
}