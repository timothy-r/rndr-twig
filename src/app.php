<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

use Ace\Request\Message as RequestMessage;
use Ace\Store\Redis as RedisStore;
use Ace\Twig\StoreLoader as StoreLoader;

use Predis\Client;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();
$config = new Config();
$store = new RedisStore(
    new Client($config->getStoreDsn(), ['exceptions' => true])
);
$loader = new StoreLoader($store);

$template_dir = __DIR__.'/templates';

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => $template_dir,
        'twig.options' => ['cache' => __DIR__ . '/cache']
    ]
);

$app['twig']->addLoader($loader);

$logger = new Logger("log");
$logger->pushHandler(new ErrorLogHandler());

$app->get('/', function () use ($app) {
    return new Response(json_encode(
        ['name' => 'render', 'desc' => 'Renders templates']),
        200,
        ['Content-Type' => 'application/json']
    );
});

/**
 * Respond with the raw template file contents
 */
$app->get("{path}", function(Request $req, $path) use ($app, $logger, $store){

    $path = '/' . $path;
    $logger->addDebug("Getting template at $path");
    $template = $store->get($path);

    return new Response($template['content'], 200);
})->assert('path', '.+');

/**
 * Render the template specified by path using the request data
 */
$app->post("{path}", function(Request $req, $path) use ($app){

    $message = new RequestMessage($req);
    $req_vars = $message->getData();
    $path = '/' . $path;
    
    try {
        // Render the template
        $result = $app['twig']->loadTemplate($path)->render($req_vars);
        // Ought to try to figure out the response content type
        return new Response($result, 200);
    } catch (Exception $ex) {
        return new Response($ex->getMessage(), 404, ['Content-Type' => 'text/plain']);
    }
})->assert('path', '.+');

/**
 * Add a template at path with contents of the request body
 */
$app->put("{path}", function(Request $req, $path) use ($app, $logger, $store) {

    $path = '/' . $path;
    $logger->addDebug("New template at $path");
    $store->set($path, $req->getContent(), $req->headers->get('Content-Type'));

    return new Response('', 200);

})->assert('path', '.+');

$app->error(function (Exception $e) use($logger) {
    $logger->addError($e->getMessage());
    return new Response($e->getMessage());
});

return $app;
