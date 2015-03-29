<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

use Ace\Request\Message as RequestMessage;
use Ace\Twig\StoreLoader;
use Ace\Store\Factory as StoreFactory;
use Ace\Store\NotFoundException;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();
$factory = new StoreFactory(new Config());

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.options' => ['cache' => __DIR__ . '/cache']
    ]
);

$store = $factory->create();
$app['twig']->setLoader(new StoreLoader($store));

$logger = new Logger('log');
$logger->pushHandler(new ErrorLogHandler());

/**
 * Respond with the raw template file contents
 */
$app->get("{path}", function(Request $req, $path) use ($app, $logger, $store){

    $path = '/' . $path;
    try {
        $template = $store->get($path);
        return new Response($template['content'], 200);
    } catch (NotFoundException $ex) {
        return new Response('', 404);
    }
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
    $store->set($path, $req->getContent(), $req->headers->get('Content-Type'));

    return new Response('', 200);

})->assert('path', '.+');

$app->error(function (Exception $e) use($logger) {
    $logger->addError($e->getMessage());
    return new Response($e->getMessage());
});

return $app;
