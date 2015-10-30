<?php namespace Ace\Provider;

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Ace\Twig\StoreLoader;
use Silex\ServiceProviderInterface;

/**
 * Provides Twig services to the application
 * customised to use a template loader that stores templates in redis
 */
class TwigProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $cache_dir = $app['config']->getTemplateCacheDir();

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