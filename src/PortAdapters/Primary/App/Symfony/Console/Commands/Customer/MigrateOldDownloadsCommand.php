<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Customer;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\ServiceClient\InventoryClient;
use Storytale\CustomerActivity\Application\Query\Customer\OldCustomerDataProvider;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\DownloadProcessingService;
use Storytale\PortAdapters\Secondary\Console\AbstractMigrateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateOldDownloadsCommand
 * @package Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Customer
 * @deprecated
 */
class MigrateOldDownloadsCommand extends AbstractMigrateCommand
{
    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var OldCustomerDataProvider */
    private OldCustomerDataProvider $oldCustomerDataProvider;

    /** @var DownloadProcessingService */
    private DownloadProcessingService $downloadProcessingService;

    public function __construct(
        CustomerRepository $customerRepository,
        DomainSession $domainSession,
        OldCustomerDataProvider $oldCustomerDataProvider,
        DownloadProcessingService $downloadProcessingService,
        InventoryClient $inventoryClient
    )
    {
        $this->customerRepository = $customerRepository;
        $this->domainSession = $domainSession;
        $this->oldCustomerDataProvider = $oldCustomerDataProvider;
        $this->downloadProcessingService = $downloadProcessingService;
        parent::__construct('old:migrateDownloads', $inventoryClient);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $downloadCount = 90000;
        $this->start($input, $output, $downloadCount, true);

        $count = 100;
        $page = 1;

        while (true) {
            $this->registerStatus('get data from old base');
            $downloads = $this->oldCustomerDataProvider->getDownloads($count, $page);
            $page++;
            if (count($downloads) < 1) {
                break;
            }

            foreach ($downloads as $downloadData) {
                $oldCustomerId = $downloadData['user_id'] ?? null;
                $oldIllustrationId = $downloadData['product_id'] ?? null;

                $createdDate = null;
                if (isset($downloadData['timestamp_date'])) {
                    try {
                        $createdDate = new \DateTime($downloadData['timestamp_date']);
                    } catch (\Exception $e) {
                        $createdDate = null;
                    }
                }

                if ($oldCustomerId === null) {
                    $this->registerError("Get download with empty user_id");
                    continue;
                }
                if ($oldIllustrationId === null) {
                    $this->registerError("Get download with empty product_id");
                    continue;
                }

                $newIllustrationId = $this->getIllustrationIdByOldId($oldIllustrationId);
                if ($newIllustrationId == null) {
                    $this->registerError("Illustration with old_id $oldIllustrationId not found");
                    continue;
                }

                $customer = $this->customerRepository->getByOldId($oldCustomerId);
                if (!$customer instanceof Customer) {
                    $this->registerError("Customer with old_id $oldCustomerId not found");
                    continue;
                }

                $this->downloadProcessingService->migrateDownload($customer, $newIllustrationId, $createdDate);
                $this->successSave();
            }
            $this->domainSession->flush();
        }

        $this->finish();
    }
}