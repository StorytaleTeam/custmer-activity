<?php

namespace Storytale\CustomerActivity\Application\Command\Customer\ExternalEvent;

use Storytale\Contracts\EventBus\ExternalEvent;
use Storytale\Contracts\EventBus\ExternalEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\Platform\StorytaleShopPlatform;
use Storytale\Contracts\SharedEvents\User\NewUserWasRegistered;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscriptionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscriptionRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;

class OnNewUserWasRegisteredHandler implements ExternalEventHandler
{
    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var CustomerFactory */
    private CustomerFactory $customerFactory;

    /** @var NewsletterSubscriptionFactory */
    private NewsletterSubscriptionFactory $newsletterSubscriptionFactory;

    /** @var NewsletterSubscriptionRepository  */
    private NewsletterSubscriptionRepository $newsletterSubscriptionRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        DomainSession $domainSession,
        CustomerFactory $customerFactory,
        NewsletterSubscriptionFactory $newsletterSubscriptionFactory,
        NewsletterSubscriptionRepository $newsletterSubscriptionRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->domainSession = $domainSession;
        $this->customerFactory = $customerFactory;
        $this->newsletterSubscriptionFactory = $newsletterSubscriptionFactory;
        $this->newsletterSubscriptionRepository = $newsletterSubscriptionRepository;
    }

    public function handler(ExternalEvent $event): void
    {
        if ($event instanceof NewUserWasRegistered) {
            $customerData = $event->jsonSerialize();
            $customerData = $customerData['user'] ?? null;
            if (empty($customerData)) {
                /** @todo логировать, без остановки скрипта */
                throw new ApplicationException('Get NewUserWasRegistered event with empty data.');
            }

            if (isset($customerData['system']) && $customerData['system'] === StorytaleShopPlatform::SYSTEM_NAME) {
                $customer = $this->customerFactory->createFromArray($customerData);
                $this->customerRepository->save($customer);

                $anonsExist = false;
                $heatingExist = false;
                $newsletterSubscriptions = $this->newsletterSubscriptionRepository->getByEmail($customer->getEmail());
                foreach ($newsletterSubscriptions as $newsletterSubscription) {
                    $newsletterSubscription->assignCustomer($customer);
                    $newsletterSubscription->subscribe();
                    if ($newsletterSubscription->getType() === NewsletterSubscription::TYPE_ANONS) {
                        $anonsExist = true;
                    }
                    if ($newsletterSubscription->getType() === NewsletterSubscription::TYPE_HEATING) {
                        $heatingExist = true;
                    }
                }

                if ($anonsExist === false) {
                    $anonsSubscription = $this->newsletterSubscriptionFactory->build($customer->getEmail(), NewsletterSubscription::TYPE_ANONS, $customer);
                    $customer->addNewsletterSubscription($anonsSubscription);
                }
                if ($heatingExist === false) {
                    $heatingSubscription = $this->newsletterSubscriptionFactory->build($customer->getEmail(), NewsletterSubscription::TYPE_HEATING, $customer);
                    $customer->addNewsletterSubscription($heatingSubscription);
                }

                $this->domainSession->flush();
                $this->domainSession->close();
            }
        }
    }
}