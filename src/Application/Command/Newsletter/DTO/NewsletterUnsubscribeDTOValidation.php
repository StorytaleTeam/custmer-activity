<?php

namespace Storytale\CustomerActivity\Application\Command\Newsletter\DTO;

use Storytale\CustomerActivity\Application\ValidationException;

class NewsletterUnsubscribeDTOValidation
{
    public function validate(NewsletterSubscriptionDTO $dto): bool
    {
        if ($dto->getNewsletterSubscriptionUuid() !== null) {
            return true;
        }

        if ($dto->getCustomerId() === null) {
            throw new ValidationException('Need not empty `customerId` param.');
        }
        if ($dto->getNewsletterType() === null) {
            throw new ValidationException('Need not empty `newsletterType` param.');
        }

        return true;
    }
}