<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();

//$finder = new Ace\TemplateFinder(__DIR__.'/../templates', 'twig');

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
    // try to find the template
    //$template = $finder->find($path);
    $req_vars = json_decode($req->getContent(), 1);

    $result = $twig->loadTemplate($path . '.twig')->render($req_vars);

    if ($result) {
        // render with var in body of request (use Content-Type to figure out how to decode body)
        // respond with rendered result
        return new Response($result, 200);
    } else {
        return new Response('', 404);
    }
});

return $app;
