<?php namespace Ace\Provider;

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Ace\Twig\StoreLoader;
use Silex\ServiceProviderInterface;

/**
 * Provides Twig services to the application
 * customised to use a template loader that stores templates in redis
 */
class Twig implements ServiceProviderInterface
{
    /**
     * @var string
     */
    private $cache_dir;

    public function __construct($dir)
    {
        $this->cache_dir = $dir;
    }

    public function register(Application $app)
    {
        $app->register(
            new TwigServiceProvider(),
            [
                'twig.options' => ['cache' => $this->cache_dir]
            ]
        );
    }

    public function boot(Application $app)
    {
        $app['twig']->setLoader(new StoreLoader($app['template.store']));
    }
}