<?php
/*
 * @author timrodger
 * Date: 29/03/15
 */
class Config
{
    /**
     * @var string
     */
    private $base_dir;

    public function __construct($base_dir)
    {
        $this->base_dir = $base_dir;
    }

    public function getBaseDir()
    {
        return $this->base_dir;
    }

    public function getStoreDsn()
    {
        // should contain a string like this 'tcp://172.17.0.154:6379'
        return getenv('REDIS_PORT');
    }
}