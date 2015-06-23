<?php namespace Ace\Provider;

use Silex\Application;
use Ace\Store\Factory as StoreFactory;
use Silex\ServiceProviderInterface;

/**
 * Provides the store for the application
 */
class Store implements ServiceProviderInterface
{

    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
        $factory = new StoreFactory($app['config']);
        $app['template.store'] = $factory->create();
    }
}