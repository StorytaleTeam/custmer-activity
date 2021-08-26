<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Newsletter;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Query\Customer\CustomerDataProvider;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscriptionFactory;
use Storytale\PortAdapters\Secondary\Console\AbstractConsoleCommand;
use Storytale\PortAdapters\Secondary\Console\ConsoleProgressBarTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitNewsletterForCustomersCommand extends AbstractConsoleCommand
{
    use ConsoleProgressBarTrait;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var CustomerDataProvider */
    private CustomerDataProvider $customerDataProvider;

    /** @var NewsletterSubscriptionFactory */
    private NewsletterSubscriptionFactory $newsletterSubscriptionFactory;

    /** @var DomainSession */
    private DomainSession $domainSession;

    public function __construct(
        CustomerRepository $customerRepository,
        CustomerDataProvider $customerDataProvider,
        NewsletterSubscriptionFactory $newsletterSubscriptionFactory,
        DomainSession $domainSession
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerDataProvider = $customerDataProvider;
        $this->newsletterSubscriptionFactory = $newsletterSubscriptionFactory;
        $this->domainSession = $domainSession;
        parent::__construct('newsletter:initForCustomers', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::setInput($input);
        parent::setOutput($output);
        $customersCount = $this->customerDataProvider->count();
        $this->pbStart($customersCount * 2);
        $count = 20;
        $page = 1;

        while (true) {
            $customers = $this->customerRepository->getBatch($count, $page);
            $page++;
            if (count($customers) < 1) {
                break;
            }

            foreach ($customers as $customer) {
                $anonsExist = false;
                $heatingExist = false;
                foreach ($customer->getNewsletterSubscriptions() as $newsletterSubscription) {
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
                    $this->pbSuccessSave();
                } else {
                    $this->pbAlreadyExist();
                }
                if ($heatingExist === false) {
                    $heatingSubscription = $this->newsletterSubscriptionFactory->build($customer->getEmail(), NewsletterSubscription::TYPE_HEATING, $customer);
                    $customer->addNewsletterSubscription($heatingSubscription);
                    $this->pbSuccessSave();
                } else {
                    $this->pbAlreadyExist();
                }

                $this->domainSession->flush();
            }
            $this->domainSession->close();
        }
        $this->pbFinish();

        return 1;
    }
}