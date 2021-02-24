<?php

namespace Storytale\CustomerActivity\Application;

interface DTOValidation
{
    /**
     * @param $dto
     * @return bool
     * @throws ValidationException
     */
    public function validate($dto): bool;
}