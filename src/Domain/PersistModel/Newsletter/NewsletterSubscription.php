<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Newsletter;

use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class NewsletterSubscription extends AbstractEntity
{
    public const TYPE_ANONS = 'anons';
    public const TYPE_HEATING = 'heating';

    private int $id;

    /** @var string */
    private string $email;

    /** @var bool */
    private bool $isActive;

    /** @var string */
    private string $type;

    /** @var string */
    private string $uuid;

    /** @var Customer|null */
    private ?Customer $customer;

    public function __construct(
        string $email, bool $isActive,
        string $type, string $uuid,
        ?Customer $customer = null
    )
    {
        $this->email = $email;
        $this->isActive = $isActive;
        $this->type = $type;
        $this->uuid = $uuid;
        $this->customer = $customer;
        parent::__construct();
    }

    public function unsubscribe(): void
    {
        $this->isActive = false;
    }

    public function subscribe(): void
    {
        $this->isActive = true;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param Customer $customer
     * @throws DomainException
     */
    public function assignCustomer(Customer $customer): void
    {
        if ($this->customer === null) {
            $this->customer = $customer;
        } else {
            throw new DomainException('Customer already exist.');
        }
    }
}