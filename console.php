<?php

use Symfony\Component\Dotenv\Dotenv;
use Storytale\Contracts\Context\EnvContext;
use Storytale\Contracts\Context\CliContext;
use Storytale\PortAdapters\Primary\IocContainer\Symfony\SymfonyIocContainer;
use Storytale\PortAdapters\Secondary\Logger\Chain\ChainLogger;
use Storytale\PortAdapters\Primary\ErrorHandler\Logged\LoggedErrorHandler;
use Storytale\PortAdapters\Primary\App\Symfony\SymfonyCliApp;

$realPath = realpath(__DIR__);

include $realPath . '/vendor/autoload.php';

if (file_exists($realPath . '/deploy/.env')) {
    (new Dotenv(true))->loadEnv($realPath . '/deploy/.env');
}

$envContext = new EnvContext(time(), null, false, getenv());
$cliContext = new CliContext(time(), $envContext, false, $argv, $_SERVER);

$symfonyIoc = SymfonyIocContainer::buildWithXmlConfig(
    $realPath . '/config/symfony-ioc.xml',
    $realPath,
    $cliContext
);

/** @var ChainLogger $logger */
$logger = $symfonyIoc->get('storytale.portAdapters.secondary.logger.chainLogger');

$errorHandler = new LoggedErrorHandler(true, $logger);

$errorHandler->registerExceptionHandler($cliContext);
$errorHandler->registerErrorHandler($cliContext);

$cliAppConfigPath = $realPath . '/config/symfony-cli-config.php';

$cliApp = SymfonyCliApp::buildWithConfig($cliAppConfigPath, $realPath);

$cliApp->dispatch(
    $symfonyIoc,
    $cliContext,
    $errorHandler->getCustomExceptionHandler($cliContext)
);
