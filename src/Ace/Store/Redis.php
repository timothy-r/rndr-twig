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
     * @throws UnavailableException
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
     * @return array
     * @throws UnavailableException
     * @throws NotFoundException
     */
    public function get($path)
    {
        try {
            $result = $this->client->hmget($path, 'content', 'type', 'last-modified');
            if (is_array($result) && !is_null($result[0]) && !is_null($result[1]) && !is_null($result[2])) {
                return ['content' => $result[0], 'type' => $result[1], 'last-modified' => $result[2]];
            } else {
                throw new NotFoundException("Template '$path'' not found");
            }
        } catch (ServerException $ex){
            throw new UnavailableException($ex->getMessage());
        }
    }

    /**
     * @return array
     */
    public function listAll()
    {

    }

    /**
     * Remove the named template
     *
     * @param $path
     * @return int
     * @throws UnavailableException
     */
    public function delete($path)
    {
        try {
            return $this->client->del($path);
        } catch (ServerException $ex){
            throw new UnavailableException($ex->getMessage());
        }
    }
}