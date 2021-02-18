<?php

/** ВЫНЕСТИ НАСТРОЙКИ СРЕДЫ В DOCKER */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_USER_DEPRECATED);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Moscow');

use Symfony\Component\Dotenv\Dotenv;
use Storytale\Contracts\Context\EnvContext;
use Storytale\Contracts\Context\HttpContext;
use Storytale\PortAdapters\Primary\IocContainer\Symfony\SymfonyIocContainer;
use Storytale\PortAdapters\Secondary\Logger\Chain\ChainLogger;
use Storytale\PortAdapters\Primary\ErrorHandler\Logged\LoggedErrorHandler;
use Storytale\PortAdapters\Primary\App\Zend\ZendWebApp;

$realPath = realpath(dirname(__DIR__));

include $realPath . '/vendor/autoload.php';

if (file_exists($realPath . '/config/.env')) {
    (new Dotenv(true))->loadEnv($realPath . '/config/.env');
}

$envContext = new EnvContext(time(), null, false, getenv());
$httpContext = new HttpContext(time(), $envContext, false, $_GET, $_POST, $_COOKIE, $_SERVER);

$iocContainer = SymfonyIocContainer::buildWithXmlConfig(
    $realPath . '/config/symfony-ioc.xml',
    $realPath, $httpContext
);

/** @var ChainLogger $logger */
$logger = $iocContainer->get('storytale.portAdapters.secondary.logger.chainLogger');
$errorHandler = new LoggedErrorHandler(true, $logger);
$errorHandler->registerExceptionHandler($httpContext);
$errorHandler->registerErrorHandler($httpContext);

$zendAppConfigPath = $realPath . '/config/zend-application.config.php';
$webApp = ZendWebApp::buildWithConfig($zendAppConfigPath, $realPath);

$webApp->dispatch(
    $iocContainer,
    $httpContext,
    $errorHandler->getCustomExceptionHandler($httpContext)
);