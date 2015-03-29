<?php namespace Ace\Store;

use Predis\Client as PredisClient;
use Predis\Response\ServerException;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class Redis implements StoreInterface
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
        try {
            $this->client->hmset($path, 'content', $contents, 'type', $type, 'last-modified', time());
        } catch (ServerException $ex){
            throw new UnavailableException($ex->getMessage());
        }
    }

    /**
     * Return the template contents for $path
     * @param $path
     */
    public function get($path)
    {
        try {
            $result = $this->client->hmget($path, 'content', 'type', 'last-modified');
            return ['content' => $result[0], 'type' => $result[1], 'last-modified' => $result[2]];
        } catch (ServerException $ex){
            throw new UnavailableException($ex->getMessage());
        }
    }
}