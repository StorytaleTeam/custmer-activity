<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Application\Command\Order\OrderService;
use Storytale\CustomerActivity\Application\ValidationException;

class OrderPositionDTOValidation
{
    public function validate(OrderPositionDTO $dto): bool
    {
        if ($dto->getProductId() === null) {
            throw new ValidationException('Need not empty `id` param for product position.');
        }
        if ($dto->getProductType() === null) {
            throw new ValidationException('Need not empty `type` param for product position.');
        }
        if (!in_array($dto->getProductType(), OrderService::SUPPORTED_PRODUCT_TYPES)) {
            throw new ValidationException('Unsupported `type` param for product position.');
        }
        if ($dto->getCount() === null) {
            throw new ValidationException('Need not empty `count` param for product position.');
        }

        return true;
    }
}