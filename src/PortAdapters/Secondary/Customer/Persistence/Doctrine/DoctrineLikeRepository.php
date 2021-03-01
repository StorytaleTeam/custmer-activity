<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Customer\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerLike;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\LikeRepository;

class DoctrineLikeRepository implements LikeRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(CustomerLike::class);
    }

    public function getByCustomerAndIllustration(int $customerId, int $illustrationId): ?CustomerLike
    {
        return $this->repository->findOneBy(['customer' => $customerId, 'illustrationId' => $illustrationId]);
    }

    public function delete(CustomerLike $customerLike): void
    {
        $this->entityManager->remove($customerLike);
    }
}