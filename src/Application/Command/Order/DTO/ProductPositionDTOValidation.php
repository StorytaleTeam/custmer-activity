<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPositionsService;

class ProductPositionDTOValidation
{
    public function validate(ProductPositionDTO $dto): bool
    {
        if ($dto->getProductId() === null) {
            throw new ValidationException('Need not empty `id` param for product position.');
        }
        if ($dto->getProductType() === null) {
            throw new ValidationException('Need not empty `type` param for product position.');
        }
        if (!in_array($dto->getProductType(), ProductPositionsService::SUPPORTED_PRODUCT_TYPES)) {
            throw new ValidationException('Unsupported `type` param for product position.');
        }
        if ($dto->getCount() === null) {
            throw new ValidationException('Need not empty `count` param for product position.');
        }

        return true;
    }
}