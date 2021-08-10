<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Application\ValidationException;

class OrderPositionDTOValidation
{
    public function validate(OrderPositionDTO $dto): bool
    {
        if ($dto->getProductId() === null) {
            throw new ValidationException('Need not empty `id` param for order position.');
        }
        if ($dto->getProductType() === null) {
            throw new ValidationException('Need not empty `type` param for product position.');
        }

        return true;
    }
}