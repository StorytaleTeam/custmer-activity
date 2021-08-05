<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Order\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\AbstractOrder;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderRepository;

class DoctrineOrderRepository implements OrderRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(AbstractOrder::class);
    }

    public function save(AbstractOrder $order): void
    {
        $this->entityManager->persist($order);
    }

    public function get(int $id): ?AbstractOrder
    {
        return $this->repository->find($id);
    }

    public function getByIdAndCustomer(int $orderId, int $customerId): ?AbstractOrder
    {
        return $this->repository->findOneBy(['id' => $orderId, 'customer' => $customerId]);
    }
}