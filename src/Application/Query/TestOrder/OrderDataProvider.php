<?php

namespace Storytale\SkeletonDev\Application\Query\TestOrder;

interface OrderDataProvider
{
    public function find(int $id);
}