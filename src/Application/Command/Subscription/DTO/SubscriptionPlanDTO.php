<?php

namespace Storytale\CustomerActivity\Application\Command\Subscription\DTO;

class SubscriptionPlanDTO
{
    /** @var string|null */
    private ?string $name;

    /** @var float|null */
    private ?float $price;

    /** @var string|null */
    private ?string $description;

    /** @var string|null */
    private ?string $durationLabel;

    /** @var int|null */
    private ?int $durationCount;

    /** @var string|null */
    private ?string $chargePeriodLabel;

    /** @var int|null */
    private ?int $chargePeriodCount;

    /** @var int|null */
    private ?int $downloadLimit;

    /** @var int|null */
    private ?int $status;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ? trim($data['name']) : null;
        $this->price = $data['price'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->downloadLimit = $data['downloadLimit'] ?? null;
        $this->status = $data['status'] ?? null;

        $this->durationCount = $data['duration_count'] ?? null;
        $this->durationLabel = $data['duration_label'] ?? null;

        $this->chargePeriodLabel = $data['chargePeriod']['label'] ?? null;
        $this->chargePeriodCount = $data['chargePeriod']['count'] ?? null;
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
    public function getDescription(): ?string
    {
        return $this->description;
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
     * @return string|null
     */
    public function getChargePeriodLabel(): ?string
    {
        return $this->chargePeriodLabel;
    }

    /**
     * @return int|null
     */
    public function getChargePeriodCount(): ?int
    {
        return $this->chargePeriodCount;
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