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
     * @return StoreInterface
     */
    public function create()
    {
        $dsn = $this->config->getStoreDsn();
        if (!empty($dsn)) {
            return new RedisStore(
                new Client($this->config->getStoreDsn(), ['exceptions' => true])
            );
        } else {
            return new MemoryStore;
        }
    }
}