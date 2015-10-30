<?php namespace Ace\Provider;

use Silex\Application;
use Ace\Store\Factory as StoreFactory;
use Silex\ServiceProviderInterface;

/**
 * Provides the store for the application
 */
class StoreProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        $app['template.store'] = (new StoreFactory($app['config']))->create();
    }
}