<?php namespace Ace\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * Handles exceptions
 */
class ErrorHandler implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->error(function (Exception $e) use($app) {
            $app['logger']->addError($e->getMessage());
            return new Response($e->getMessage());
        });

    }

    public function boot(Application $app)
    {

    }
}