<?php namespace Ace\Store;

use Predis\Client;
use Config;
use Ace\Store\Redis as RedisStore;
use Ace\Store\Memory as MemoryStore;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class Factory
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
       $this->config = $config;
    }

    /**
     * If an in memory store has been explicitly configured
     * then use that otherwise use redis
     *
     * @return StoreInterface
     */
    public function create()
    {
        $dsn = $this->config->getStoreDsn();

        if ('MEMORY' == $dsn) {
            return new MemoryStore;
        } else {
            return new RedisStore(
                new Client($dsn, ['exceptions' => true])
            );
        }
    }
}