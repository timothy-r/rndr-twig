<?php

use Silex\Application;
use Silex\Provider\MonologServiceProvider;

use Ace\Provider\ConfigProvider;
use Ace\Provider\StoreProvider;
use Ace\Provider\TwigProvider;
use Ace\Provider\RouteProvider;
use Ace\Provider\ErrorHandlerProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register(new ConfigProvider(__DIR__ . '/cache'));

$app->register(new MonologServiceProvider());
$app['monolog.logfile'] = "php://stdout";
$app['monolog.name'] = 'render';

$app->register(new ErrorHandlerProvider());

$app->register(new StoreProvider());

$app->register(new TwigProvider());
$app->register(new RouteProvider());

return $app;
