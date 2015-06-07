<?php namespace Ace\Provider;

use Silex\Application;
use Ace\Store\Factory as StoreFactory;

/**
 * Provides the store for the application
 */
class Store
{

    public function register(Application $app)
    {
        $factory = new StoreFactory($app['config']);
        $app['template.store'] = $factory->create();
    }
}