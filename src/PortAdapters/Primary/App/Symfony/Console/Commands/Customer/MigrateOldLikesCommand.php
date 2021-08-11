<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Customer;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\ServiceClient\InventoryClient;
use Storytale\CustomerActivity\Application\Query\Customer\OldCustomerDataProvider;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerLike;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerLikeFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\LikeRepository;
use Storytale\PortAdapters\Secondary\Console\AbstractMigrateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateOldLikesCommand
 * @package Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Customer
 * @deprecated
 */
class MigrateOldLikesCommand extends AbstractMigrateCommand
{
    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var OldCustomerDataProvider */
    private OldCustomerDataProvider $oldCustomerDataProvider;

    /** @var LikeRepository */
    private LikeRepository $likeRepository;

    /** @var CustomerLikeFactory */
    private CustomerLikeFactory $likeFactory;

    public function __construct(
        CustomerRepository $customerRepository,
        DomainSession $domainSession,
        OldCustomerDataProvider $oldCustomerDataProvider,
        LikeRepository $likeRepository,
        CustomerLikeFactory $likeFactory,
        InventoryClient $inventoryClient
    )
    {
        $this->customerRepository = $customerRepository;
        $this->domainSession = $domainSession;
        $this->oldCustomerDataProvider = $oldCustomerDataProvider;
        $this->likeRepository = $likeRepository;
        $this->likeFactory = $likeFactory;
        parent::__construct('old:migrateLikes', $inventoryClient);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $likeCount = 7500;
        $this->start($input, $output, $likeCount, true);
        $count = 100;
        $page = 1;

        while (true) {
            $this->registerStatus('get data from old base');
            $likes = $this->oldCustomerDataProvider->getLikes($count, $page);
            $page++;
            if (count($likes) < 1) {
                break;
            }
            foreach ($likes as $likeData) {
                $oldCustomerId = $likeData['user_id'] ?? null;
                $oldIllustrationId = isset($likeData['identifier']) ?
                    substr($likeData['identifier'], strpos($likeData['identifier'], '_')+1)
                    : null;

                $createdDate = null;
                if (isset($likeData['created_at'])) {
                    try {
                        $createdDate = new \DateTime($likeData['created_at']);
                    } catch (\Exception $e) {
                        $createdDate = null;
                    }
                }

                if ($oldIllustrationId === null) {
                    $this->registerError("Get like with empty user_id");
                    continue;
                }
                if ($oldIllustrationId === null) {
                    $this->registerError("Get like with empty identifier");
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

                $cloneLike = $this->likeRepository->getByCustomerAndIllustration($customer->getId(), $newIllustrationId);
                if ($cloneLike instanceof CustomerLike) {
                    $this->alreadyExist();
                    continue;
                }

                $newLike = $this->likeFactory->create($customer, $newIllustrationId, $createdDate);
                $customer->like($newLike);
                $this->successSave();
            }
            $this->domainSession->flush();
        }

        $this->finish();
    }
}