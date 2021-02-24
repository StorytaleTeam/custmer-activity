<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Application\DTOValidation;
use Storytale\CustomerActivity\Application\ValidationException;

class SubscriptionSigningDTOValidation implements DTOValidation
{
    public function validate($dto): bool
    {
        if (!$dto instanceof SubscriptionSigningDTO) {
            throw new ApplicationException('Invalid DTO given. Need SubscriptionSigningDTO.');
        }
        if (empty($dto->getCustomerId())) {
            throw new ValidationException('Need not empty `customerId` param.');
        }
        if (empty($dto->getSubscriptionPlanId())) {
            throw new ValidationException('Need not empty `subscriptionPlanId` param.');
        }

        return true;
    }
}