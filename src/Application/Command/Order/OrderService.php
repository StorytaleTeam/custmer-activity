<?php

namespace Storytale\CustomerActivity\Application\Command\Order;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Command\Order\DTO\ConfirmOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\DTO\ConfirmOrderDTOValidation;
use Storytale\CustomerActivity\Application\Command\Order\DTO\CreateOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\DTO\CreateOrderDTOValidation;
use Storytale\CustomerActivity\Application\Command\Order\DTO\OrderHydrator;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\AbstractOrder;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderPositionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Product\IProductBuilder;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;

class OrderService
{
    /** @var OrderRepository */
    private OrderRepository $orderRepository;

    /** @var SubscriptionPlanRepository */
    private SubscriptionPlanRepository $subscriptionPlanRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var CreateOrderDTOValidation */
    private CreateOrderDTOValidation $createOrderDTOValidation;

    /** @var OrderFactory */
    private OrderFactory $orderFactory;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var ConfirmOrderDTOValidation */
    private ConfirmOrderDTOValidation $confirmOrderDTOValidation;

    /** @var OrderHydrator */
    private OrderHydrator $orderHydrator;

    /** @var IProductBuilder */
    private IProductBuilder $productBuilder;

    /** @var OrderPositionFactory */
    private OrderPositionFactory $orderPositionFactory;

    public function __construct(
        OrderRepository $orderRepository,
        SubscriptionPlanRepository $subscriptionPlanRepository,
        DomainSession $domainSession,
        CreateOrderDTOValidation $createOrderDTOValidation,
        OrderFactory $orderFactory,
        CustomerRepository $customerRepository,
        ConfirmOrderDTOValidation $confirmOrderDTOValidation,
        OrderHydrator $orderHydrator,
        IProductBuilder $productBuilder,
        OrderPositionFactory $orderPositionFactory
    )
    {
        $this->orderRepository = $orderRepository;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->domainSession = $domainSession;
        $this->createOrderDTOValidation = $createOrderDTOValidation;
        $this->orderFactory = $orderFactory;
        $this->customerRepository = $customerRepository;
        $this->confirmOrderDTOValidation = $confirmOrderDTOValidation;
        $this->orderHydrator = $orderHydrator;
        $this->productBuilder = $productBuilder;
        $this->orderPositionFactory = $orderPositionFactory;
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

            $orderPositions = [];
            {
                foreach ($createOrderDTO->getOrderPositionsDTO() as $orderPositionDTO) {
                    try {
                        $product = $this->productBuilder
                            ->build($orderPositionDTO->getProductType(), $orderPositionDTO->getProductId());
                    } catch (DomainException $e) {
                        throw new ValidationException($e->getMessage());
                    }

                    $orderPositions[] = $this->orderPositionFactory->build($product);
                }
            }

            try {
                $order = $this->orderFactory->build($customer, $orderPositions);
            } catch (DomainException $e) {
                throw new ValidationException($e->getMessage());
            }

            $this->orderRepository->save($order);
            $this->domainSession->flush();

            $result['order'] = $this->orderHydrator->toArray($order);
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
            if (!$order instanceof AbstractOrder) {
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

    /**
     * @param ConfirmOrderDTO $confirmOrderDTO
     * @return OperationResponse
     * @todo confirmOrderDTO здесь не к месту, но было лень заморачиваться :(
     * @todo надо бы переименовать ConfirmOrderDTO во что-нибудь
     */
    public function getOne(ConfirmOrderDTO $confirmOrderDTO): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->confirmOrderDTOValidation->validate($confirmOrderDTO);
            $order = $this->orderRepository->getByIdAndCustomer($confirmOrderDTO->getOrderId(), $confirmOrderDTO->getCustomerId());
            if (!$order instanceof AbstractOrder) {
                throw new ValidationException(
                    'Order with id ' . $confirmOrderDTO->getOrderId()
                    . ' not found for this customer.'
                );
            }

            $success = true;
            $result['order'] = $this->orderHydrator->toArray($order);
        } catch (ValidationException $e) {
            $message = $e->getMessage();
            $success = false;
        }

        return new OperationResponse($success, $result, $message);
    }
}