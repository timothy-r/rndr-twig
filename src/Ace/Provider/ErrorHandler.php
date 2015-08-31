<?php namespace Ace\Provider;

use Ace\Store\NotFoundException;
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
    }

    public function boot(Application $app)
    {
        $app->error(function (Exception $e) use($app) {

            $app['logger']->addError($e->getMessage());

            $code = ($e instanceof NotFoundException) ? 404 : 500;

            return new Response($e->getMessage(), $code);
        });

    }
}