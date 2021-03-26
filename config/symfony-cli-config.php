<?php

return [
    'commandMap' => [
        'subscriptionPlan:sync-with-paddle' => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\SubscriptionPlan\SyncWithPaddleCommand::class,
        'subscription:prolongate' => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription\ProlongateSubscriptionCommand::class,
    ],
];
