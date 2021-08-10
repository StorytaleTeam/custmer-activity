<?php

namespace Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Order;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Query\Subscription\OldSubscriptionDataProvider;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\PortAdapters\Secondary\Console\AbstractMigrateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    public function __construct(
        OrderRepository $orderRepository,
        OrderFactory $orderFactory,
        DomainSession $domainSession,
        OldSubscriptionDataProvider $oldSubscriptionDataProvider,
        CustomerRepository $customerRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->domainSession = $domainSession;
        $this->oldSubscriptionDataProvider = $oldSubscriptionDataProvider;
        $this->customerRepository = $customerRepository;
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
                $orderPosition = $this->oldSubscriptionDataProvider->getOrderProducts($oldOrder['ID']);
                if (empty($orderPosition)) {
                    $this->registerError('Get order with empty positions  ' . $oldOrder['ID'] ?? null);
                    continue;
                }
                $oldCustomerId = $orderPosition['customer_id'] ?? null;
                if ($oldCustomerId === null) {
                    $this->registerError('Get order with empty customer_id  ' . $oldOrder['ID'] ?? null);
                    continue;
                }
                $customer = $this->customerRepository->get($oldCustomerId);
                if (!$customer instanceof Customer) {
                    $this->registerError('Not found customer for oldOrder ' . $oldOrder['ID'] ?? null);
                    continue;
                }
                $createdDate = null;
                if (isset($oldOrder['date_created'])) {
                    try {
                        $createdDate = new \DateTime($oldOrder['date_created']);
                    } catch (\Exception $e) {
                        $createdDate = null;
                    }
                }
                /** @todo build OrderPosition */

                $order = $this->orderFactory->build($customer, $createdDate);


                var_dump($order);die;
            }
        }
    }
}