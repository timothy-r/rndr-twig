<?php namespace Ace;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * @author tim rodger
 *
 * Date: 30/10/15
 */
class Cache
{
    /**
     * @var string
     */
    private $cache_dir;

    /**
     * @param $cache_dir string
     */
    public function __construct($cache_dir)
    {
        $this->cache_dir = $cache_dir;
    }

    /**
     * Remove the cached files
     *
     * @param $name string
     */
    public function clear($name)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->cache_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file_info) {
            $todo = ($file_info->isDir() ? 'rmdir' : 'unlink');
            $todo($file_info->getRealPath());
        }
    }
}