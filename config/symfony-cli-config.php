<?php

return [
    'commandMap' => [
        'subscriptionPlan:sync-with-paddle'     => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\SubscriptionPlan\SyncWithPaddleCommand::class,
        'subscription:prolongate'               => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription\ProlongateSubscriptionCommand::class,
        'newsletter:initForCustomers'           => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Newsletter\InitNewsletterForCustomersCommand::class,

        'old:migrateNewsletterSubscription'     => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Newsletter\MigrateOldNewsletterSubscriptionsCommand::class,
//        'old:activateOldSubscription'           => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription\ActivateOldSubscriptionsCommand::class,
//        'old:migrateMemberships'                => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription\MigrateOldMembershipsCommand::class,
//        'old:migrateSubscriptions'              => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Subscription\MigrateOldSubscriptionCommand::class,
//        'old:migrateOrder'                      => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Order\MigrateOldOrdersCommand::class,
//        'old:migrateDownloads'                  => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Customer\MigrateOldDownloadsCommand::class,
//        'old:migrateLikes'                      => \Storytale\CustomerActivity\PortAdapters\Primary\App\Symfony\Console\Commands\Customer\MigrateOldLikesCommand::class,
    ],
];
