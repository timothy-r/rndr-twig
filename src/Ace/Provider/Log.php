<?php namespace Ace\Provider;

use Silex\Application;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

/**
 * @author timrodger
 * Date: 07/06/15
 */
class Log
{
    public function register(Application $app)
    {
        $app['logger'] = new Logger('log');
        $app['logger']->pushHandler(new ErrorLogHandler());
    }
}