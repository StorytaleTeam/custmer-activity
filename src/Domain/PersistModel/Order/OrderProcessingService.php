<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Order;

interface OrderProcessingService
{
    public function wasPaid(AbstractOrder $order);


}