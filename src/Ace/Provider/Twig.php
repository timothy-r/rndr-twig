<?php namespace Ace\Provider;

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Ace\Twig\StoreLoader;
use Silex\ServiceProviderInterface;

/**
 * Provides Twig services to the application
 */
class Twig implements ServiceProviderInterface
{
    private $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function register(Application $app)
    {
        $cache_dir = $this->dir . '/cache';

        $app->register(
            new TwigServiceProvider(),
            [
                'twig.options' => ['cache' => $cache_dir]
            ]
        );
    }

    public function boot(Application $app)
    {
        $app['twig']->setLoader(new StoreLoader($app['template.store']));
    }
}