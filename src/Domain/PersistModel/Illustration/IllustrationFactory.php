<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Illustration;

class IllustrationFactory
{
    public function buildFromInventoryResponse(array $data): Illustration
    {
        return new Illustration(
            $data['id'],
            $data['isFree'] ?? false
        );
    }
}