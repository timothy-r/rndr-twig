<?php namespace Ace\Store;

use Predis\Client as PredisClient;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class Redis
{
    /**
     * @var PredisClient
     */
    private $client;

    public function __construct(PredisClient $client)
    {
        $this->client = $client;
    }

    /**
     * Set the contents for this template path
     * set last modified property to now
     *
     * @param $path
     * @param $contents
     * @param $type
     * @throws Ace\Store\UnavailableException
     */
    public function set($path, $contents, $type)
    {
        $this->client->hmset($path, 'content', $contents, 'last-modified', time(), 'type', $type);
    }
}