<?php namespace Ace\Store;

use Ace\Store\NotFoundException;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class Memory implements StoreInterface
{
    /**
     * @var array
     */
    private $data = [];

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
        $this->data[$path] = ['content' => $content, 'type' => $type, 'last-modified' => time()];
    }

    /**
     * Return the template contents for $path
     * @param $path
     */
    public function get($path)
    {
        if (isset($this->data[$path])) {
            return $this->data[$path];
        } else {
            throw new NotFoundException("Template '$path' not found");
        }
    }

    public function delete($path)
    {
        unset($this->data[$path]);
    }
}