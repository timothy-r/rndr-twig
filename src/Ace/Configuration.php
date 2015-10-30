<?php namespace Ace;

/*
 * @author timrodger
 * Date: 29/03/15
 */
class Configuration
{
    /**
     * @var string
     */
    private $template_cache;

    public function __construct($template_cache)
    {
        $this->template_cache = $template_cache;
    }

    /**
     * Should be a string something like this 'tcp://172.17.0.154:6379'
     * @return string
     */
    public function getStoreDsn()
    {
        return getenv('REDIS_PORT');
    }

    /**
     * @return string
     */
    public function getTemplateCacheDir()
    {
        return $this->template_cache;
    }
}