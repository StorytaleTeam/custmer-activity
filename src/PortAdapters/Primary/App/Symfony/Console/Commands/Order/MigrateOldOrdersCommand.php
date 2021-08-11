<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Order;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Query\Subscription\OldSubscriptionDataProvider;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\AbstractOrder;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderPositionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\CustomerActivity\PortAdapters\Secondary\Product\ProductBuilder;
use Storytale\PortAdapters\Secondary\Console\AbstractMigrateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateOldOrdersCommand
 * @package Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Order
 * @deprecated
 */
class MigrateOldOrdersCommand extends AbstractMigrateCommand
{
    /** @var OrderRepository */
    private OrderRepository $orderRepository;

    /** @var OrderFactory */
    private OrderFactory $orderFactory;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var OldSubscriptionDataProvider */
    private OldSubscriptionDataProvider $oldSubscriptionDataProvider;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var ProductBuilder */
    private ProductBuilder $productBuilder;

    /** @var OrderPositionFactory */
    private OrderPositionFactory $orderPositionFactory;

    public function __construct(
        OrderRepository $orderRepository,
        OrderFactory $orderFactory,
        DomainSession $domainSession,
        OldSubscriptionDataProvider $oldSubscriptionDataProvider,
        CustomerRepository $customerRepository,
        ProductBuilder $productBuilder,
        OrderPositionFactory $orderPositionFactory
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->domainSession = $domainSession;
        $this->oldSubscriptionDataProvider = $oldSubscriptionDataProvider;
        $this->customerRepository = $customerRepository;
        $this->productBuilder = $productBuilder;
        $this->orderPositionFactory = $orderPositionFactory;
        parent::__construct('old:migrateOrder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start($input, $output, 3000, false);

        $count = 50;
        $page = 1;

        while (true) {
            $oldOrders = $this->oldSubscriptionDataProvider->getOrders($count, $page);
            $page++;
            if (count($oldOrders) < 1) {
                break;
            }

            foreach ($oldOrders as $oldOrder) {
                $sameOrder = $this->orderRepository->getByOldId($oldOrder['ID']);
                if ($sameOrder instanceof AbstractOrder) {
                    $this->alreadyExist();
                    continue;
                }
                $oldOrderPosition = $this->oldSubscriptionDataProvider->getOrderProducts($oldOrder['ID']);
                $orderMetas = $this->oldSubscriptionDataProvider->getMetaForPost($oldOrder['ID']);
                foreach ($orderMetas as $orderMeta) {
                    if ($orderMeta['meta_key'] === '_customer_user') {
                        $oldCustomerId = $orderMeta['meta_value'] ?? null;

                    }
                }
                if (empty($oldCustomerId)) {
                    continue;
                }


                if (empty($oldOrderPosition)) {
                    $this->registerError('Get order with empty positions  ' . $oldOrder['ID'] ?? null);
                    continue;
                }
                $oldProductId = $oldOrderPosition['product_id'] ?? null;
                if ($oldProductId === null) {
                    $this->registerError('Get order with empty product_id  ' . $oldOrder['ID'] ?? null);
                    continue;
                }

                $customer = $this->customerRepository->getByOldId($oldCustomerId);
                if (!$customer instanceof Customer) {
                    $this->registerError('Not found customer ' . $oldCustomerId . ' for oldOrder ' . $oldOrder['ID'] ?? null);
                    continue;
                }
                $createdDate = null;
                if (isset($oldOrder['post_date'])) {
                    try {
                        $createdDate = new \DateTime($oldOrder['post_date']);
                    } catch (\Exception $e) {
                        $createdDate = null;
                    }
                }

                $orderPositions = [];
                try {
                    $product = $this->productBuilder->buildSubscriptionPlanByOldId($oldProductId);
                    $orderPositions[] = $this->orderPositionFactory->build($product);
                } catch (\Exception $e) {
                    $this->registerError($e->getMessage());
                    continue;
                }

                $order = $this->orderFactory->buildOrderSubscription($customer, $orderPositions, $createdDate, $oldOrder['ID']);
                $this->orderRepository->save($order);
                $this->domainSession->flush();
                $this->successSave();
            }
            $this->domainSession->close();
        }

        $this->finish();
    }
}