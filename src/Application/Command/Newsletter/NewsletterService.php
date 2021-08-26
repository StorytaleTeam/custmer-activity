<?php

namespace Storytale\CustomerActivity\Application\Command\Newsletter;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
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

    public function __construct(
        DomainSession $domainSession,
        NewsletterSubscriptionRepository $newsletterRepository,
        NewsletterSubscriptionFactory $newsletterSubscriptionFactory,
        CustomerRepository $customerRepository
    )
    {
        $this->domainSession = $domainSession;
        $this->newsletterRepository = $newsletterRepository;
        $this->newsletterSubscriptionFactory = $newsletterSubscriptionFactory;
        $this->customerRepository = $customerRepository;
    }

    public function subscribeEmail(?string $email): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            if ($email === null) {
                throw new ValidationException('Need not empty `email` param.');
            }
            $isExist = false;
            $newsletters = $this->newsletterRepository->getByEmail($email);
            foreach ($newsletters as $newsletter) {
                if ($newsletter->getType() === NewsletterSubscription::TYPE_ANONS) {
                    $newsletter->subscribe();
                    $isExist = true;
                }
            }

            if ($isExist !== true) {
                $customer = $this->customerRepository->getByEmail($email);
                $newAnonsSubscription = $this->newsletterSubscriptionFactory->build($email, NewsletterSubscription::TYPE_ANONS, $customer);
                $this->newsletterRepository->save($newAnonsSubscription);
            }

            $this->domainSession->flush();
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }

    public function unsubscribeByUuid(?string $uuid): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            if ($uuid === null) {
                throw new ValidationException('Need not empty `uuid` param.');
            }
            $newsletter = $this->newsletterRepository->getByUuid($uuid);
            if ($newsletter instanceof NewsletterSubscription) {
                $newsletter->unsubscribe();
            } else {
                throw new ValidationException('Newsletter subscription with this uuid not found.');
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