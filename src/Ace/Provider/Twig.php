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
    public function register(Application $app)
    {
        $cache_dir = $app['config']->getBaseDir() . '/cache';

        $app->register(
            new TwigServiceProvider(),
            [
                'twig.options' => ['cache' => $cache_dir]
            ]
        );

        $app['twig']->setLoader(new StoreLoader($app['template.store']));
    }

    public function boot(Application $app)
    {

    }
}