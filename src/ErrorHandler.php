<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles exceptions
 */
class ErrorHandler
{
    public function register(Application $app)
    {
        $app->error(function (Exception $e) use($app) {
            $app['logger']->addError($e->getMessage());
            return new Response($e->getMessage());
        });

    }
}