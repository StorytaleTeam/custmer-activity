<?php

namespace Storytale\CustomerActivity\Application\Command\Order;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\Command\Order\DTO\CreateOrderDTO;
use Storytale\CustomerActivity\Application\Command\Order\DTO\CreateOrderDTOValidation;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\ProductPositionFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;

class OrderService
{
    public const SUPPORTED_PRODUCT_TYPES = ['subscriptionPlan'];

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

    public function __construct(
        OrderRepository $orderRepository,
        SubscriptionPlanRepository $subscriptionPlanRepository,
        DomainSession $domainSession,
        ProductPositionFactory $productPositionFactory,
        CreateOrderDTOValidation $createOrderDTOValidation
    )
    {
        $this->orderRepository = $orderRepository;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->domainSession = $domainSession;
        $this->productPositionFactory = $productPositionFactory;
        $this->createOrderDTOValidation = $createOrderDTOValidation;
    }

    public function create(CreateOrderDTO $createOrderDTO): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $this->createOrderDTOValidation->validate($createOrderDTO);

            $success = true;
        } catch (ValidationException $e) {
            $message = $e->getMessage();
            $success = false;
        }

        return new OperationResponse($success, $result, $message);
    }

}