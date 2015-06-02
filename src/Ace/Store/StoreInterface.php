<?php namespace Ace\Store;

/**
 * @author timrodger
 * Date: 29/03/15
 */
interface StoreInterface
{
    public function set($path, $contents, $type);

    public function get($path);

    public function delete($path);
}