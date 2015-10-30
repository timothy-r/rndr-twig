<?php namespace Ace\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * Handles exceptions
 */
class ErrorHandlerProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        $app->error(function (Exception $e) use($app) {

            $app['logger']->addError($e->getMessage());

            // should really use $e->getCode() but twig's exception codes are hard coded to 0
            switch (get_class($e)){
                case 'Twig_Error_Loader':
                case 'Ace\Store\NotFoundException':
                    $code = 404;
                    break;
                default:
                    $code = 500;
            }

            return new Response($e->getMessage(), $code);
        });

    }
}