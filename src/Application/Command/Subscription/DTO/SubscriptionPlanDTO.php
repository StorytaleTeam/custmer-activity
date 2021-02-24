<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

class SubscriptionPlanDTO
{
    /** @var string|null */
    private ?string $name;

    /** @var float|null */
    private ?float $price;

    /** @var int|null */
    private ?int $duration;

    /** @var int|null */
    private ?int $downloadLimit;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ? trim($data['name']) : null;
        $this->price = $data['price'] ?? null;
        $this->duration = $data['duration'] ?? null;
        $this->downloadLimit = $data['downloadLimit'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @return int|null
     */
    public function getDownloadLimit(): ?int
    {
        return $this->downloadLimit;
    }
}