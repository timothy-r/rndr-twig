<?php

use Silex\Application;

use Ace\Provider\Config as ConfigProvider;
use Ace\Provider\Store as StoreProvider;
use Ace\Provider\Twig as TwigProvider;
use Ace\Provider\Log as LogProvider;
use Ace\Provider\Route as RouteProvider;
use Ace\Provider\ErrorHandler as ErrorHandlerProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register(new ConfigProvider(__DIR__));
$app->register(new LogProvider());
$app->register(new ErrorHandlerProvider());

$app->register(new StoreProvider());
$app->register(new TwigProvider(__DIR__));
$app->register(new RouteProvider());

return $app;
