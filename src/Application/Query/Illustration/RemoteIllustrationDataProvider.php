<?php

namespace Storytale\CustomerActivity\Application\Query\Illustration;

use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Illustration\Illustration;

interface RemoteIllustrationDataProvider
{
    /**
     * @param int $illustrationId
     * @return array|null
     * @throws ValidationException
     */
    public function getZip(int $illustrationId): ?array;

    /**
     * @param int $illustrationId
     * @return Illustration|null
     */
    public function get(int $illustrationId): ?Illustration;
}