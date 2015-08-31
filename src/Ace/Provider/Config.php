<?php namespace Ace\Provider;

use Ace\Configuration;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * @author timrodger
 * Date: 23/06/15
 */
class Config implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['config'] = new Configuration();
    }

    public function boot(Application $app)
    {

    }

}