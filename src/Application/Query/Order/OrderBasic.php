<?php

namespace Storytale\CustomerActivity\Application\Query\Order;

class OrderBasic implements \JsonSerializable
{
    /** @var int|null */
    private ?int $id;

    /** @var string|null */
    private ?string $createdDate;

    /** @var int|null */
    private ?int $status;

    /** @var float|null */
    private ?float $totalPrice;

    /** @var string|null */
    private ?string $productPositions;

    public function jsonSerialize()
    {
        $response = [
            'id' => $this->id ?? null,
            'createdDate' => $this->createdDate ?? null,
            'status' => $this->status ?? null,
            'totalPrice' => $this->totalPrice ?? null,
        ];

        if (isset($this->productPositions)) {
            $response['productPositions'] = json_decode($this->productPositions);
        }

        return $response;
    }
}