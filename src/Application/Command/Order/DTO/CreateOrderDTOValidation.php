<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Application\ValidationException;

class CreateOrderDTOValidation
{
    /** @var OrderPositionDTOValidation */
    private OrderPositionDTOValidation $orderPositionDTOValidation;

    public function __construct(OrderPositionDTOValidation $orderPositionDTOValidation)
    {
        $this->orderPositionDTOValidation = $orderPositionDTOValidation;
    }

    public function validate(CreateOrderDTO $dto): bool
    {
        if ($dto->getCustomerId() === null) {
            throw new ValidationException('Need not empty `customerId` param.');
        }
        if (!is_array($dto->getOrderPositionsDTO()) || count($dto->getOrderPositionsDTO()) === 0) {
            throw new ValidationException('Need not empty `positions` array.');
        }
        foreach ($dto->getOrderPositionsDTO() as $positionDTO) {
            $this->orderPositionDTOValidation->validate($positionDTO);
        }

        return true;
    }
}