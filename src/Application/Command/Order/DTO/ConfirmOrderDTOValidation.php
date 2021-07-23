<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Application\ValidationException;

class ConfirmOrderDTOValidation
{
    public function validate(ConfirmOrderDTO $dto): bool
    {
        if ($dto->getCustomerId() === null) {
            throw new ValidationException('Need not empty `customerId` param.');
        }
        if ($dto->getOrderId() === null) {
            throw new ValidationException('Need not empty `orderId` param.');
        }

        return true;
    }
}