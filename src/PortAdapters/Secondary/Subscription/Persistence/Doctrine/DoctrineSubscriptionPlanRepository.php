<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlan;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\SubscriptionPlanRepository;

class DoctrineSubscriptionPlanRepository implements SubscriptionPlanRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(SubscriptionPlan::class);
    }

    public function get(int $id): ?SubscriptionPlan
    {
        return $this->repository->find($id);
    }

    public function save(SubscriptionPlan $subscriptionPlan): void
    {
        $this->entityManager->persist($subscriptionPlan);
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }
}