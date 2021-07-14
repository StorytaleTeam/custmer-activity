<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Order\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\Order;
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
        $this->repository = $entityManager->getRepository(Order::class);
    }

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
    }

    public function get(int $id): ?Order
    {
        return $this->repository->find($id);
    }
}