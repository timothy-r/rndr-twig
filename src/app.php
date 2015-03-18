<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();

//$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');
//$twig = new Twig_Environment($loader);//

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    ['twig.path' => __DIR__.'/../templates']
);

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
        // render with var in body of request
        $result = $app['twig']->loadTemplate($path . '.twig')->render($req_vars);
        // Ought to try to figure out the response content type
        // respond with rendered result
        return new Response($result, 200);
    } catch (Exception $ex) {
        return new Response('', 404);
    }
});

// add router for other request methods to reject them

return $app;
