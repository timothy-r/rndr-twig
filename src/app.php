<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => __DIR__.'/../templates',
        'twig.options' => ['cache' => __DIR__ . '/../cache']
    ]
);

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
 * Catch all post requests to any path
 */
$app->post("{path}", function(Request $req, $path) use ($app){
    // Should use request content-type to convert request into an array and not assume json
    // there's a lib for this in php, right?
    $req_vars = json_decode($req->getContent(), 1);

    try {
        // try to find the template
        $result = $app['twig']->loadTemplate($path . '.twig')->render($req_vars);
        // Ought to try to figure out the response content type
        return new Response($result, 200);
    } catch (Exception $ex) {
        return new Response($ex->getMessage(), 404);
    }
})->assert('path', '.+');

$app->error(function (Exception $e, $code) use($logger) {
    $logger->addError($e->getMessage());
    return new Response($e->getMessage(), $code, ['Content-Type' => 'text/plain']);
});

return $app;