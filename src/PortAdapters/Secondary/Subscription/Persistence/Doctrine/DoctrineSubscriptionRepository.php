<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionRepository;

class DoctrineSubscriptionRepository implements SubscriptionRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Subscription::class);
    }

    public function get(int $id): ?Subscription
    {
        return $this->repository->find($id);
    }

    public function save(Subscription $subscription): void
    {
        $this->entityManager->persist($subscription);
    }
}