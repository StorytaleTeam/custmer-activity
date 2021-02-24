<?php

namespace Storytale\CustomerActivity\Domain;

interface SpecificationInterface
{
    /**
     * @param $candidate
     * @return bool
     */
    public function isSatisfiedBy($candidate): bool;
}