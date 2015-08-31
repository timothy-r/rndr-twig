<?php namespace Ace;

/*
 * @author timrodger
 * Date: 29/03/15
 */
class Configuration
{
    /**
     * Should be a string something like this 'tcp://172.17.0.154:6379'
     * @return string
     */
    public function getStoreDsn()
    {
        return getenv('REDIS_PORT');
    }
}