<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Customer\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;

class DoctrineCustomerRepository implements CustomerRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Customer::class);
    }

    public function get(int $id): ?Customer
    {
        return $this->repository->find($id);
    }

    public function getByOldId(int $oldId): ?Customer
    {
        return $this->repository->findOneBy(['oldId' => $oldId]);
    }

    public function save(Customer $customer): void
    {
        $this->entityManager->persist($customer);
    }
}