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

class OnNewUserWasRegisteredHandler implements ExternalEventHandler
{
    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var CustomerFactory */
    private CustomerFactory $customerFactory;

    public function __construct(CustomerRepository $customerRepository, DomainSession $domainSession, CustomerFactory $customerFactory)
    {
        $this->customerRepository = $customerRepository;
        $this->domainSession = $domainSession;
        $this->customerFactory = $customerFactory;
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
                $this->domainSession->flush();
                $this->domainSession->close();
            }
        }
    }
}