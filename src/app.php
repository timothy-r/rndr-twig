<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => __DIR__.'/templates',
        'twig.options' => ['cache' => __DIR__ . '/cache']
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
 * Use variables supplied as json, form data or via query parameters as template vars
 */
$app->post("{path}", function(Request $req, $path) use ($app){
    // Should use request content-type to convert request into an array and not assume json
    // there's a lib for this in php, right?
    $req_vars = [];

    switch($req->headers->get('Content-Type')) {
        case 'application/json':
            $req_vars = json_decode($req->getContent(), 1);
            break;
        case 'application/x-www-form-urlencoded':
        case 'multipart/form-data':
            $req_vars = $req->request->all();
            break;
    }

    $query = $req->query->all();
    if (is_array($query)) {
        // values in $req_vars overwrite those in $query
        $req_vars = array_merge($query, $req_vars);
    }

    try {
        // try to find the template
        $result = $app['twig']->loadTemplate($path . '.twig')->render($req_vars);
        // Ought to try to figure out the response content type
        return new Response($result, 200);
    } catch (Exception $ex) {
        return new Response($ex->getMessage(), 404, ['Content-Type' => 'text/plain']);
    }
})->assert('path', '.+');

$app->error(function (Exception $e) use($logger) {
    $logger->addError($e->getMessage());
    return new Response($e->getMessage());
});

return $app;
