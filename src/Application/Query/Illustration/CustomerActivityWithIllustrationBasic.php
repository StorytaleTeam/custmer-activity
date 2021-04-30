<?php

namespace Storytale\CustomerActivity\Application\Query\Illustration;

class CustomerActivityWithIllustrationBasic implements \JsonSerializable
{
    /** @var int|null */
    private ?int $illustrationId;

    /** @var bool|null */
    private ?bool $isLiked;

    /** @var bool|null */
    private ?bool $isDownloaded;

    public function jsonSerialize()
    {
        return [
            'illustrationId' => $this->illustrationId ?? null,
            'isLiked' => $this->isLiked ?? null,
            'isDownloaded' => $this->isDownloaded ?? null,
        ];
    }
}