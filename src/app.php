<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();

$finder = new Ace\TemplateFinder(__DIR__.'/../templates', 'twig');

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
   // try to find the template
   // render with var in body of request (use Content-Type to figure out how to decode body)
   // respond with rendered result

});

return $app;
