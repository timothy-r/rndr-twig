<?php namespace Ace\Store;

use Ace\Store\UnavailableException;
/**
 * @author timrodger
 * Date: 29/03/15
 */
class Unavailable implements StoreInterface
{
    /**
     * Set the contents for this template path
     * set last modified property to now
     *
     * @param $path
     * @param $content
     * @param $type
     */
    public function set($path, $content, $type)
    {
        throw new UnavailableException('Store is not available');
    }

    /**
     * Return the template contents for $path
     * @param $path
     */
    public function get($path)
    {
        throw new UnavailableException('Store is not available');
    }

    /**
     * @throws UnavailableException
     */
    public function listAll()
    {
        throw new UnavailableException('Store is not available');
    }

    /**
     * @param $path
     * @throws UnavailableException
     */
    public function delete($path)
    {
        throw new UnavailableException('Store is not available');
    }
}