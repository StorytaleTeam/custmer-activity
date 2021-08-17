<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\Membership;
use Storytale\CustomerActivity\Domain\PersistModel\Subscription\MembershipRepository;

/**
 * Class DoctrineMembershipRepository
 * @package Storytale\CustomerActivity\PortAdapters\Secondary\Subscription\Persistence\Doctrine
 * @deprecated
 */
class DoctrineMembershipRepository implements MembershipRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Membership::class);
    }

    public function get(int $id): ?Membership
    {
        return $this->repository->find($id);
    }

    public function getByOldId(int $oldId): ?Membership
    {
        return $this->repository->findOneBy(['oldId' => $oldId]);
    }

    public function save(Membership $membership): void
    {
        $this->entityManager->persist($membership);
    }
}