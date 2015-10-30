<?php namespace Ace\Provider;

use Ace\Cache;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Ace\Request\MessageAdapter as RequestMessage;

/**
 * Configures routing
 */
class RouteProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        /**
         * Respond with the raw template file contents
         */
        $app->get("{path}", function(Request $req, $path) use ($app){

            $app['logger']->info("Getting template for '$path'");

            $path = '/' . $path;
            $template = $app['template.store']->get($path);
            return new Response($template['content'], 200, ["Content-Type" => $template['type']]);

        })->assert('path', '.+');

        /**
         * Render the template specified by path using the request data
         */
        $app->post("{path}", function(Request $req, $path) use ($app){

            $app['logger']->info("Rendering template for '$path'");

            $message = new RequestMessage($req);
            $path = '/' . $path;

            // Render the template
            $result = $app['twig']->loadTemplate($path)->render($message->getData());
            // Ought to try to figure out the response content type
            return new Response($result, 200);
            
        })->assert('path', '.+');

        /**
         * Add a template at path with contents of the request body
         */
        $app->put("{path}", function(Request $req, $path) use ($app) {

            $app['logger']->info("Setting template for '$path'");

            $path = '/' . $path;
            $app['template.store']->set($path, $req->getContent(), $req->headers->get('Content-Type'));

            // clear the cache here
            $cache = new Cache($app['config']->getTemplateCacheDir());
            $cache->clear($path);

            return new Response('', 200);

        })->assert('path', '.+');

        /**
         * Removes a template
         */
        $app->delete("{path}", function($path) use ($app) {

            $app['logger']->info("Removing template for '$path'");

            $path = '/' . $path;
            $app['template.store']->delete($path);

            return new Response('', 200);

        })->assert('path', '.+');

    }
}