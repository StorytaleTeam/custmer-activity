<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\DTOValidation;
use Storytale\CustomerActivity\Application\ValidationException;

class SubscriptionPlanDTOValidation implements DTOValidation
{
    public function validate($dto): bool
    {
        if (!$dto instanceof SubscriptionPlanDTO) {
            throw new ApplicationException('Invalid DTO given. Need SubscriptionPlanDTO.');
        }

        if (empty($dto->getName())) {
            throw new ValidationException('Need not empty `name` param.');
        }
        if (empty($dto->getPrice())) {
            throw new ValidationException('Need not empty `price` param.');
        }
        if (empty($dto->getDuration())) {
            throw new ValidationException('Need not empty `duration` param.');
        }
        if (empty($dto->getDownloadLimit())) {
            throw new ValidationException('Need not empty `downloadLimit` param.');
        }

        return true;
    }
}