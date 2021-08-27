<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Newsletter\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscription;
use Storytale\CustomerActivity\Domain\PersistModel\Newsletter\NewsletterSubscriptionRepository;

class DoctrineNewsletterSubscriptionRepository implements NewsletterSubscriptionRepository
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(NewsletterSubscription::class);
    }

    public function getByEmail(string $email): array
    {
        return $this->repository->findBy(['email' => $email]);
    }

    public function getByEmailAndType(string $email, string $type): ?NewsletterSubscription
    {
        return $this->repository->findOneBy(['email' => $email, 'type' => $type]);
    }

    public function getByUuid(string $uuid): ?NewsletterSubscription
    {
        return $this->repository->findOneBy(['uuid' => $uuid]);
    }

    public function save(NewsletterSubscription $newsletterSubscription): void
    {
        $this->entityManager->persist($newsletterSubscription);
    }
}