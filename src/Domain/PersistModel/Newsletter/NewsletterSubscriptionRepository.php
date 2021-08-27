<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Newsletter;

interface NewsletterSubscriptionRepository
{
    /**
     * @param string $email
     * @return NewsletterSubscription[]
     */
    public function getByEmail(string $email): array;

    /**
     * @param string $email
     * @param string $type
     * @return NewsletterSubscription|null
     */
    public function getByEmailAndType(string $email, string $type): ?NewsletterSubscription;

    /**
     * @param string $uuid
     * @return NewsletterSubscription|null
     */
    public function getByUuid(string $uuid): ?NewsletterSubscription;

    /**
     * @param NewsletterSubscription $newsletterSubscription
     */
    public function save(NewsletterSubscription $newsletterSubscription): void;
}