<?php

use Silex\Application;

use Ace\Provider\Store as StoreProvider;
use Ace\Provider\Twig as TwigProvider;
use Ace\Provider\Log as LogProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app['config'] = new Config(__DIR__);

$log_provider = new LogProvider();
$log_provider->register($app);

$store_provider = new StoreProvider();
$store_provider->register($app);

$twig_provider = new TwigProvider();
$twig_provider->register($app);

$error_handler = new ErrorHandler();
$error_handler->register($app);

$router = new Router();
$router->register($app);

return $app;
