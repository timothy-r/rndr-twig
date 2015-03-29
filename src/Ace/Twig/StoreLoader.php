<?php namespace Ace\Twig; 

use Ace\Store\StoreInterface;
use Ace\Store\UnavailableException;
use Twig_LoaderInterface;
use Twig_Error_Loader;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class StoreLoader implements Twig_LoaderInterface
{
    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @param StoreInterface $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * @param string $name
     * @throws Twig_Error_Loader
     * @return string
     */
    public function getSource($name)
    {
        try {
            $template = $this->store->get($name);
            return $template['content'];
        } catch (UnavailableException $ex){
            throw new Twig_Error_Loader($ex->getMessage());
        }
    }

    /**
     * @param string $name
     * @throws Twig_Error_Loader
     * @return string
     */
    public function getCacheKey($name)
    {
        try {
            $this->store->get($name);
            return md5($name);
        } catch (UnavailableException $ex){
            throw new Twig_Error_Loader($ex->getMessage());
        }
    }

    /**
     * @param string $name
     * @param \timestamp $time
     * @throws Twig_Error_Loader
     * @return boolean
     */
    public function isFresh($name, $time)
    {
        try {
            $template = $this->store->get($name);
            return $template['last-modified'] < $time;
        } catch (UnavailableException $ex){
            throw new Twig_Error_Loader($ex->getMessage());
        }
    }

    public function exists($name)
    {
        try {
            $this->store->get($name);
            return true;
        } catch (UnavailableException $ex) {
            return false;
        }
    }
}