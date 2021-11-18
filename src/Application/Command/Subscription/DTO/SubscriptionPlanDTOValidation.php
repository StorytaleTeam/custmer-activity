<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\DTOValidation;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\TimeRange;

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
        if (empty($dto->getDescription())) {
            throw new ValidationException('Need not empty `description` param.');
        }
        if (empty($dto->getDurationCount())) {
            throw new ValidationException('Need not empty `duration_count` param.');
        }
        if (empty($dto->getDurationLabel())) {
            throw new ValidationException('Need not empty `duration_label` param.');
        } elseif (!in_array($dto->getDurationLabel(), TimeRange::AVAILABLE_LABEL)) {
            throw new ValidationException('Unsupported value for `duration_label` param.');
        }
        if (empty($dto->getChargePeriodCount())) {
            throw new ValidationException('Need not empty `chargePeriod[count]` param.');
        }
        if (empty($dto->getChargePeriodLabel())) {
            throw new ValidationException('Need not empty `chargePeriod[label]` param.');
        } elseif (!in_array($dto->getChargePeriodLabel(), TimeRange::AVAILABLE_LABEL)) {
            throw new ValidationException('Unsupported value for `chargePeriod[label]` param.');
        }
        if (empty($dto->getDownloadLimit())) {
            throw new ValidationException('Need not empty `downloadLimit` param.');
        }

        return true;
    }
}