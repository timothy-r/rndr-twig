<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Ace\Request\Message as RequestMessage;
use Ace\Store\NotFoundException;

/**
 * Configures routing
 */
class Router
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {

        /**
         * Respond with the raw template file contents
         */
        $app->get("{path}", function(Request $req, $path) use ($app){

            $path = '/' . $path;
            try {
                $template = $app['template.store']->get($path);
                return new Response($template['content'], 200, ["Content-Type" => $template['type']]);
            } catch (NotFoundException $ex) {
                return new Response("Template '$path' not found", 404, ["Content-Type" => 'text/plain']);
            }
        })->assert('path', '.+');

        /**
         * Render the template specified by path using the request data
         */
        $app->post("{path}", function(Request $req, $path) use ($app){

            $message = new RequestMessage($req);
            $path = '/' . $path;

            try {
                // Render the template
                $result = $app['twig']->loadTemplate($path)->render($message->getData());
                // Ought to try to figure out the response content type
                return new Response($result, 200);
            } catch (Exception $ex) {
                return new Response($ex->getMessage(), 404, ['Content-Type' => 'text/plain']);
            }
        })->assert('path', '.+');

        /**
         * Add a template at path with contents of the request body
         */
        $app->put("{path}", function(Request $req, $path) use ($app) {

            $path = '/' . $path;
            $app['template.store']->set($path, $req->getContent(), $req->headers->get('Content-Type'));

            return new Response('', 200);

        })->assert('path', '.+');

        /**
         * Removes a template
         */
        $app->delete("{path}", function($path) use ($app) {

            $path = '/' . $path;
            $app['template.store']->delete($path);

            return new Response('', 200);

        })->assert('path', '.+');

    }
}