<?php


namespace Storytale\CustomerActivity\Application\Command\Newsletter\DTO;


use Storytale\CustomerActivity\Application\ValidationException;

class NewsletterSubscribeDTOValidation
{
    public function validate(NewsletterSubscriptionDTO $dto): bool
    {
        if ($dto->getCustomerId() === null && $dto->getEmail() === null) {
            throw new ValidationException('Need not empty `customerId` or `email` param.');
        }
        if ($dto->getNewsletterType() === null) {
            throw new ValidationException('Need not empty `newsletterType` param.');
        }

        return true;
    }
}