<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

class SubscriptionPlanDTO
{
    /** @var string|null */
    private ?string $name;

    /** @var float|null */
    private ?float $price;

    /** @var string|null */
    private ?string $durationLabel;

    /** @var int|null */
    private ?int $durationCount;

    /** @var int|null */
    private ?int $downloadLimit;

    /** @var int|null */
    private ?int $status;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ? trim($data['name']) : null;
        $this->price = $data['price'] ?? null;
        $this->durationCount = $data['duration_count'] ?? null;
        $this->durationLabel = $data['duration_label'] ?? null;
        $this->downloadLimit = $data['downloadLimit'] ?? null;
        $this->status = $data['status'] ?? null;
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
     * @return string|null
     */
    public function getDurationLabel(): ?string
    {
        return $this->durationLabel;
    }

    /**
     * @return int|null
     */
    public function getDurationCount(): ?int
    {
        return $this->durationCount;
    }

    /**
     * @return int|null
     */
    public function getDownloadLimit(): ?int
    {
        return $this->downloadLimit;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }
}