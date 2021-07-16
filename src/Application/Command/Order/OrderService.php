<?php

namespace Storytale\CustomerActivity\Application\Command\Order;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Command\Order\DTO\ConfirmOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\DTO\ConfirmOrderDTOValidation;
use Storytale\CustomerActivity\Application\Command\Order\DTO\CreateOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\DTO\CreateOrderDTOValidation;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\Order;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPositionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPositionsService;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;

class OrderService
{
    /** @var OrderRepository */
    private OrderRepository $orderRepository;

    /** @var SubscriptionPlanRepository */
    private SubscriptionPlanRepository $subscriptionPlanRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var ProductPositionFactory */
    private ProductPositionFactory $productPositionFactory;

    /** @var CreateOrderDTOValidation */
    private CreateOrderDTOValidation $createOrderDTOValidation;

    /** @var OrderFactory */
    private OrderFactory $orderFactory;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var ProductPositionsService */
    private ProductPositionsService $productPositionService;

    /** @var ConfirmOrderDTOValidation */
    private ConfirmOrderDTOValidation $confirmOrderDTOValidation;

    public function __construct(
        OrderRepository $orderRepository,
        SubscriptionPlanRepository $subscriptionPlanRepository,
        DomainSession $domainSession,
        ProductPositionFactory $productPositionFactory,
        CreateOrderDTOValidation $createOrderDTOValidation,
        OrderFactory $orderFactory,
        CustomerRepository $customerRepository,
        ProductPositionsService $productPositionService,
        ConfirmOrderDTOValidation $confirmOrderDTOValidation
    )
    {
        $this->orderRepository = $orderRepository;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->domainSession = $domainSession;
        $this->productPositionFactory = $productPositionFactory;
        $this->createOrderDTOValidation = $createOrderDTOValidation;
        $this->orderFactory = $orderFactory;
        $this->customerRepository = $customerRepository;
        $this->productPositionService = $productPositionService;
        $this->confirmOrderDTOValidation = $confirmOrderDTOValidation;
    }

    public function create(CreateOrderDTO $createOrderDTO): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->createOrderDTOValidation->validate($createOrderDTO);
            $customer = $this->customerRepository->get($createOrderDTO->getCustomerId());
            if (!$customer instanceof Customer) {
                throw new ValidationException('Customer with this id not found.');
            }
            $order = $this->orderFactory->build($customer);

            foreach ($createOrderDTO->getProductPositionsDTO() as $productPositionDTO) {
                $productPosition = $this->productPositionService->getProductPositionByDTO($productPositionDTO);
                $order->addProduct($productPosition);
            }

            $this->orderRepository->save($order);
            $this->domainSession->flush();

            $result['order']['id'] = $order->getId();
            $success = true;
        } catch (ValidationException $e) {
            $message = $e->getMessage();
            $success = false;
        }

        return new OperationResponse($success, $result, $message);
    }

    public function confirm(ConfirmOrderDTO $confirmOrderDTO): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->confirmOrderDTOValidation->validate($confirmOrderDTO);
            $order = $this->orderRepository->getByIdAndCustomer($confirmOrderDTO->getOrderId(), $confirmOrderDTO->getCustomerId());
            if (!$order instanceof Order) {
                throw new ValidationException(
                    'Order with id ' . $confirmOrderDTO->getOrderId()
                    . ' not found for this customer.'
                );
            }

            $order->confirm();
            $this->domainSession->flush();

            $success = true;
        } catch (ValidationException $e) {
            $message = $e->getMessage();
            $success = false;
        }

        return new OperationResponse($success, $result, $message);
    }
}