<?php namespace Ace\Provider;

use Ace\Configuration;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * @author timrodger
 * Date: 23/06/15
 */
class ConfigProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    private $template_cache;

    public function __construct($template_cache)
    {
        $this->template_cache = $template_cache;
    }

    public function register(Application $app)
    {
        $app['config'] = new Configuration($this->template_cache);
    }

    public function boot(Application $app)
    {

    }

}