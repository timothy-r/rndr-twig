<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();

$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');
$twig = new Twig_Environment($loader);//

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
$app->post("{path}", function(Request $req, $path) use ($app, $twig){

    // use request content-type to convert request into an array
    // there's a lib for this in php, right?
    $req_vars = json_decode($req->getContent(), 1);

    try {
        // try to find the template
        // render with var in body of request
        // respond with rendered result
        $result = $twig->loadTemplate($path . '.twig')->render($req_vars);
        return new Response($result, 200);
    } catch (Exception $ex) {
        return new Response('', 404);
    }
});

return $app;
