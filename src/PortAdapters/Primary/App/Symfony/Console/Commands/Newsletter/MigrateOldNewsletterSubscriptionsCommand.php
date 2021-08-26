<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Newsletter;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Query\Customer\OldCustomerDataProvider;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscriptionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscriptionRepository;
use Storytale\PortAdapters\Secondary\Console\AbstractConsoleCommand;
use Storytale\PortAdapters\Secondary\Console\ConsoleProgressBarTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class MigrateOldNewsletterSubscriptionsCommand
 * @package Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Newsletter
 * @deprecated
 */
class MigrateOldNewsletterSubscriptionsCommand extends AbstractConsoleCommand
{
    use ConsoleProgressBarTrait;

    /** @var OldCustomerDataProvider */
    private OldCustomerDataProvider $oldCustomerDataProvider;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var NewsletterSubscriptionRepository */
    private NewsletterSubscriptionRepository $newsletterSubscriptionRepository;

    /** @var NewsletterSubscriptionFactory */
    private NewsletterSubscriptionFactory $newsletterSubscriptionFactory;

    /** @var DomainSession */
    private DomainSession $domainSession;

    public function __construct(
        OldCustomerDataProvider $oldCustomerDataProvider,
        CustomerRepository $customerRepository,
        NewsletterSubscriptionRepository $newsletterSubscriptionRepository,
        NewsletterSubscriptionFactory $newsletterSubscriptionFactory,
        DomainSession $domainSession
    )
    {
        $this->oldCustomerDataProvider = $oldCustomerDataProvider;
        $this->customerRepository = $customerRepository;
        $this->newsletterSubscriptionRepository = $newsletterSubscriptionRepository;
        $this->newsletterSubscriptionFactory = $newsletterSubscriptionFactory;
        $this->domainSession = $domainSession;
        parent::__construct('old:migrateNewsletterSubscription', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::setInput($input);
        parent::setOutput($output);
        $oldNewsletterSubscriptionCount = 1250;
        $this->pbStart($oldNewsletterSubscriptionCount);
        $count = 100;
        $page = 1;

        while (true) {
            $oldNewsletterSubscriptions = $this->oldCustomerDataProvider->getCanceledNewsletter($count, $page);
            $page++;
            if (count($oldNewsletterSubscriptions) < 1) {
                break;
            }

            foreach ($oldNewsletterSubscriptions as $oldNewsletterSubscription) {
                $email = $oldNewsletterSubscription['email'] ?? null;
                $newsletters = $this->newsletterSubscriptionRepository->getByEmail($email);
                foreach ($newsletters as $newsletter) {
                    if ($newsletter->getType() === NewsletterSubscription::TYPE_ANONS) {
                        $newsletter->unsubscribe();
                        $this->pbSuccessSave();
                    }
                }
                $this->domainSession->flush();
            }
            $this->domainSession->close();
        }

        $this->pbFinish();
        return 1;
    }
}