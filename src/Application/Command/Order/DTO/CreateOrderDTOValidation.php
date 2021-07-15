<?php

namespace Storytale\CustomerActivity\Application\Command\Order\DTO;

use Storytale\CustomerActivity\Application\ValidationException;

class CreateOrderDTOValidation
{
    /** @var ProductPositionDTOValidation */
    private ProductPositionDTOValidation $productPositionDTOValidation;

    public function __construct(ProductPositionDTOValidation $productPositionDTOValidation)
    {
        $this->productPositionDTOValidation = $productPositionDTOValidation;
    }

    public function validate(CreateOrderDTO $dto): bool
    {
        if ($dto->getCustomerId() === null) {
            throw new ValidationException('Need not empty `customerId` param.');
        }
        if (!is_array($dto->getProductPositionsDTO()) || count($dto->getProductPositionsDTO()) === 0) {
            throw new ValidationException('Need not empty `productPositions` array.');
        }
        foreach ($dto->getProductPositionsDTO() as $positionDTO) {
            $this->productPositionDTOValidation->validate($positionDTO);
        }

        return true;
    }
}