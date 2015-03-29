<?php
/*
 * @author timrodger
 * Date: 29/03/15
 */
class Config
{
    public function getStoreDsn()
    {
        // should contain a string like this 'tcp://172.17.0.154:6379'
        return getenv('REDIS_PORT');
    }
}