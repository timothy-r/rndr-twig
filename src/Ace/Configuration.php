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
    private $root_dir;

    public function __construct($root_dir)
    {
        $this->root_dir = $root_dir;
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
        return $this->root_dir . '/cache';
    }

    /**
     * @return string
     */
    public function getTranslationDir()
    {
        return $this->root_dir . '/translations';
    }
}