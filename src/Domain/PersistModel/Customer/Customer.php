<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Customer;

use Storytale\PortAdapters\Secondary\Persistence\AbstractEntity;

class Customer extends AbstractEntity
{
    /** @var int */
    private int $id;

    /** @var array */
    private $likes;

    /** @var array */
    private $downloads;

    /** @var array */
    private $subscriptions;

    /** @var string */
    private string $email;

    /** @var bool */
    private bool $subscriptionAutoRenewal;

    /** @var string|null */
    private ?string $name;

    public function __construct(int $id, string $email, bool $subscriptionAutoRenewal, ?string $name = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->subscriptionAutoRenewal = $subscriptionAutoRenewal;
        $this->name = $name;
        parent::__construct();
    }
}