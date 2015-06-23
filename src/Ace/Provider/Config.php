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

    private $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function register(Application $app)
    {
        $app['config'] = new Configuration($this->dir);
    }

    public function boot(Application $app)
    {

    }

}