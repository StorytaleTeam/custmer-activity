<?php

namespace Storytale\CustomerActivity\Application\Query\Illustration;

use Storytale\CustomerActivity\Application\ValidationException;

interface RemoteIllustrationDataProvider
{
    /**
     * @param int $illustrationId
     * @return string|null
     * @throws ValidationException
     */
    public function getZip(int $illustrationId): ?string;
}