<?php

use Storytale\Contracts\Context\EnvContext;
use Storytale\Contracts\Context\CliContext;
use Storytale\PortAdapters\Secondary\Logger\Chain\ChainLogger;
use Storytale\PortAdapters\Primary\ErrorHandler\Logged\LoggedErrorHandler;
use Storytale\PortAdapters\Primary\IocContainer\Symfony\SymfonyIocContainer;
use Storytale\PortAdapters\Primary\App\Subscriber\SubscriberApp;

use Symfony\Component\Dotenv\Dotenv;

$realPath = realpath(__DIR__);

include $realPath . '/vendor/autoload.php';

if (file_exists($realPath . '/config/.env')) {
    (new Dotenv(true))->loadEnv($realPath . '/config/.env');
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

(new SubscriberApp())->dispatch(
    $symfonyIoc,
    $cliContext,
    $errorHandler->getCustomExceptionHandler($cliContext)
);
