<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Order\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Order\OrderSubscriptionRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Subscription;

class DoctrineOrderSubscriptionRepository implements OrderSubscriptionRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(OrderSubscription::class);
    }

    public function getBySubscription(Subscription $subscription): ?OrderSubscription
    {
        return $this->repository->findOneBy(['subscription' => $subscription]);
    }
}