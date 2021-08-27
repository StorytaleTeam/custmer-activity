<?php

namespace Storytale\CustomerActivity\Application\Command\Newsletter;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Command\Newsletter\DTO\NewsletterSubscribeDTOValidation;
use Storytale\CustomerActivity\Application\Command\Newsletter\DTO\NewsletterSubscriptionDTO;
use Storytale\CustomerActivity\Application\Command\Newsletter\DTO\NewsletterUnsubscribeDTOValidation;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscriptionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscriptionRepository;

class NewsletterService
{
    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var NewsletterSubscriptionRepository */
    private NewsletterSubscriptionRepository $newsletterRepository;

    /** @var NewsletterSubscriptionFactory */
    private NewsletterSubscriptionFactory $newsletterSubscriptionFactory;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var NewsletterSubscribeDTOValidation */
    private NewsletterSubscribeDTOValidation $newsletterSubscribeDTOValidation;

    /** @var NewsletterUnsubscribeDTOValidation */
    private NewsletterUnsubscribeDTOValidation $newsletterUnsubscribeDTOValidation;

    public function __construct(
        DomainSession $domainSession,
        NewsletterSubscriptionRepository $newsletterRepository,
        NewsletterSubscriptionFactory $newsletterSubscriptionFactory,
        CustomerRepository $customerRepository,
        NewsletterSubscribeDTOValidation $newsletterSubscribeDTOValidation,
        NewsletterUnsubscribeDTOValidation $newsletterUnsubscribeDTOValidation
    )
    {
        $this->domainSession = $domainSession;
        $this->newsletterRepository = $newsletterRepository;
        $this->newsletterSubscriptionFactory = $newsletterSubscriptionFactory;
        $this->customerRepository = $customerRepository;
        $this->newsletterSubscribeDTOValidation = $newsletterSubscribeDTOValidation;
        $this->newsletterUnsubscribeDTOValidation = $newsletterUnsubscribeDTOValidation;
    }

    public function subscribe(NewsletterSubscriptionDTO $dto): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->newsletterSubscribeDTOValidation->validate($dto);
            $actualNewsletterSubscription = null;
            $customer = null;

            if ($dto->getCustomerId() !== null) {
                $customer = $this->customerRepository->get($dto->getCustomerId());
                if (!$customer instanceof Customer) {
                    throw new ValidationException('Customer with this id not found.');
                }

                foreach ($customer->getNewsletterSubscriptions() as $newsletterSubscription) {
                    if ($newsletterSubscription->getType() === $dto->getNewsletterType()) {
                        $actualNewsletterSubscription = $newsletterSubscription;
                        break;
                    }
                }
            } else {
                $actualNewsletterSubscription = $this->newsletterRepository->getByEmailAndType($dto->getEmail(), $dto->getNewsletterType());
            }
            if (!$actualNewsletterSubscription instanceof NewsletterSubscription) {
                if (!$customer instanceof Customer) {
                    $customer = $this->customerRepository->getByEmail($dto->getEmail());
                }
                $email = $customer instanceof Customer ? $customer->getEmail() : $dto->getEmail();
                $actualNewsletterSubscription = $this->newsletterSubscriptionFactory->build($email, $dto->getNewsletterType(), $customer);
                $this->newsletterRepository->save($actualNewsletterSubscription);
            }

            $actualNewsletterSubscription->subscribe();

            $this->domainSession->flush();
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }

    public function unsubscribe(NewsletterSubscriptionDTO $dto): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->newsletterUnsubscribeDTOValidation->validate($dto);
            $actualNewsletterSubscription = null;

            if ($dto->getNewsletterSubscriptionUuid() !== null) {
                $actualNewsletterSubscription = $this->newsletterRepository->getByUuid($dto->getNewsletterSubscriptionUuid());
                if (!$actualNewsletterSubscription instanceof NewsletterSubscription) {
                    throw new ValidationException('Newsletter subscription with this uuid not found.');
                }
            } else {
                $customer = $this->customerRepository->get($dto->getCustomerId());
                if (!$customer instanceof Customer) {
                    throw new ValidationException('Customer with this id not found.');
                }
                foreach ($customer->getNewsletterSubscriptions() as $newsletterSubscription) {
                    if ($newsletterSubscription->getType() === $dto->getNewsletterType()) {
                        $actualNewsletterSubscription = $newsletterSubscription;
                        break;
                    }
                }
            }

            if ($actualNewsletterSubscription instanceof NewsletterSubscription) {
                $actualNewsletterSubscription->unsubscribe();
            }

            $this->domainSession->flush();
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }
}