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

    public function save(Subscription $subscription): void
    {
        $this->entityManager->persist($subscription);
    }

    public function get(int $id): ?Subscription
    {
        return $this->repository->find($id);
    }

    public function getByOldId(int $oldId): ?Subscription
    {
        return $this->repository->findOneBy(['oldId' => $oldId]);
    }

    public function getForProlongate(): array
    {
        $qb = $this->repository->createQueryBuilder('s')
            ->leftJoin(
                's.memberships', 'm', 'WITH',
                's.id = m.subscription AND s.currentMembershipCycle = m.cycleNumber'
            )
            ->where('s.autoRenewal = true')
            ->andWhere('m.endDate <= :today')
            ->andWhere('s.status = :statusActive')
            ->setParameters([
                'today' => new \DateTime(),
                'statusActive' => 2,
            ]);


        return $qb->getQuery()->execute();
    }

    /**
     * @return array
     * @deprecated
     */
    public function getOldForActivate(): array
    {
        $qb = $this->repository->createQueryBuilder('s')
            ->leftJoin(
                's.memberships', 'm', 'WITH',
                's.id = m.subscription AND m.cycleNumber IS NULL'
            )
            ->where('s.autoRenewal = true')
            ->andWhere('s.status = :statusActive')
            ->andWhere('s.oldId IS NOT NULL')
            ->setParameters([
                'statusActive' => 2,
            ]);

        return $qb->getQuery()->execute();
    }
}