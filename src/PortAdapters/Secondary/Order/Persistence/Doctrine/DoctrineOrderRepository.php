<?php

namespace Storytale\SkeletonDev\PortAdapters\Secondary\Order\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\SkeletonDev\Domain\PersistModel\Order\Order;
use Storytale\SkeletonDev\Domain\PersistModel\Order\OrderRepository;

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

    /**
     * @inheritDoc
     */
    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Order
    {
        return $this->repository->find($id);
    }
}