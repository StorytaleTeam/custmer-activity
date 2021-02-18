<?php

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Dotenv\Dotenv;
use Storytale\PortAdapters\Primary\IocContainer\Symfony\SymfonyIocContainer;
use Storytale\Contracts\Context\EnvContext;

$realPath = realpath(dirname(__DIR__));

if (file_exists($realPath . '/config/.env')) {
    (new Dotenv(true))->loadEnv($realPath . '/config/.env');
}

$symfonyIoc = SymfonyIocContainer::buildWithXmlConfig(
    __DIR__ . '/../config/symfony-ioc.xml',
    $realPath,
    new EnvContext(time(), null, false, getenv())
);
/** @var Connection $connection */
$connection = $symfonyIoc->get('doctrine.orm.connection');

/** @var Configuration $migrationConfiguration */
$migrationConfiguration = $symfonyIoc->get('doctrine.migrations.configuration');

/** @var EntityManagerInterface $entityManager */
$entityManager = $symfonyIoc->get('doctrine.orm.entityManager');

return new HelperSet([
    'configuration' => new ConfigurationHelper($connection, $migrationConfiguration),
    'em' => new EntityManagerHelper($entityManager),
]);
