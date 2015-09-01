<?php

use Silex\Application;
use Silex\Provider\MonologServiceProvider;

use Ace\Provider\Config as ConfigProvider;
use Ace\Provider\Store as StoreProvider;
use Ace\Provider\Twig as TwigProvider;
use Ace\Provider\Route as RouteProvider;
use Ace\Provider\ErrorHandler as ErrorHandlerProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register(new ConfigProvider());

$app->register(new MonologServiceProvider());
$app['monolog.logfile'] = "php://stdout";
$app['monolog.name'] = 'render';

$app->register(new ErrorHandlerProvider());

$app->register(new StoreProvider());

// pass cache directory to the TwigProvider - use an env var to control this
$app->register(new TwigProvider(__DIR__ . '/cache'));
$app->register(new RouteProvider());

return $app;
