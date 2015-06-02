<?php namespace Ace\Store;

use Predis\Client;
use Config;
use Ace\Store\Redis as RedisStore;
use Ace\Store\Memory as MemoryStore;
use Ace\Store\Unavailable as UnavailableStore;

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
     * then use that, otherwise use redis
     *
     * @return StoreInterface
     */
    public function create()
    {
        $dsn = $this->config->getStoreDsn();

        if ('MEMORY' == $dsn) {
            return new MemoryStore;
        } else if ('UNAVAILABLE' == $dsn) {
            return new UnavailableStore();
        } else {
            return new RedisStore(
                new Client($dsn, ['exceptions' => true])
            );
        }
    }
}