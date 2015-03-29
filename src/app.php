<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Ace\Request\Message as RequestMessage;
use Ace\Store\Redis as RedisStore;
use Predis\Client;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();
$config = new Config();
$store = new RedisStore(
    new Client($config->getStoreDsn(), ['exceptions' => true])
);

$template_dir = __DIR__.'/templates';

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => $template_dir,
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
 * Respond with the raw template file contents
 */
$app->get("{path}", function(Request $req, $path) use ($app, $logger, $store){

//    $file_path = $template_dir . '/' . $path;
//    $status_code = 404;
//    $body = '';
//    if (is_file($file_path)){
//        $status_code = 200;
//        $body = file_get_contents($file_path);
//    }
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

//    $dir = $template_dir . '/' . dirname($path);
//
//    if (!is_dir($dir)) {
//        if (!mkdir($dir, 0755, true)){
//            throw new Exception("Failed to make directory $dir");
//        }
//    }
//
//    $file_path = $dir . '/' . basename($path);
//
//    $created = !file_exists($file_path);
//    if (!file_put_contents($file_path, $req->getContent())){
//        throw new Exception("Failed to create file " . $dir . '/' . basename($path));
//    }

   // $app['twig']->clearCacheFiles();
   // $app['twig']->clearTemplateCache();

    return new Response('', 200);

})->assert('path', '.+');

$app->error(function (Exception $e) use($logger) {
    $logger->addError($e->getMessage());
    return new Response($e->getMessage());
});

return $app;
