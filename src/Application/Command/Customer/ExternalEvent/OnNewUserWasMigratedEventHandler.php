<?php

namespace Storytale\CustomerActivity\Application\Command\Customer\ExternalEvent;

use Storytale\Contracts\EventBus\ExternalEvent;
use Storytale\Contracts\EventBus\ExternalEventHandler;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\Platform\StorytaleShopPlatform;
use Storytale\Contracts\SharedEvents\User\NewUserWasMigratedEvent;
use Storytale\CustomerActivity\Application\ApplicationException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;

class OnNewUserWasMigratedEventHandler implements ExternalEventHandler
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
        if ($event instanceof NewUserWasMigratedEvent) {
            $customerData = $event->jsonSerialize();
            $customerData = $customerData['data']['user'] ?? null;
            if (empty($customerData)) {
                throw new ApplicationException('Get NewUserWasMigrated event with empty data.');
            }
            if (isset($customerData['oldId'])) {
                $cloneCustomer = $this->customerRepository->getByOldId($customerData['oldId']);
                if (!$cloneCustomer instanceof Customer) {
                    if (isset($customerData['system']) && $customerData['system'] === StorytaleShopPlatform::SYSTEM_NAME) {
                        $customer = $this->customerFactory->createFromArray($customerData);
                        $this->customerRepository->save($customer);
                        $this->domainSession->flush();
                    }
                }
            }
        }
    }
}