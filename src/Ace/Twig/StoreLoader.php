<?php namespace Ace\Twig; 

use Ace\Store\NotFoundException;
use Ace\Store\StoreInterface;
use Ace\Store\UnavailableException;
use Twig_LoaderInterface;
use Twig_Error_Loader;
use Twig_Error_Runtime;

/**
 * @author timrodger
 * Date: 29/03/15
 *
 * Loads Twig templates from a Store instance
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
        $template = $this->get($name);
        return $template['content'];
    }

    /**
     * @param string $name
     * @throws Twig_Error_Loader
     * @return string
     */
    public function getCacheKey($name)
    {
        $this->get($name);
        return md5($name);
    }

    /**
     * @param string $name
     * @param \timestamp $time
     * @throws Twig_Error_Loader
     * @return boolean
     */
    public function isFresh($name, $time)
    {
        $template = $this->get($name);
        return $template['last-modified'] < $time;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function exists($name)
    {
        try {
            $this->store->get($name);
            return true;
        } catch (NotFoundException $nex) {
            return false;
        } catch (UnavailableException $ex) {
            return false;
        }
    }

    /**
     * Get the template data - converts Store exceptions into Twig exceptions
     * @param $name
     * @return array
     * @throws Twig_Error_Loader
     */
    private function get($name)
    {
        try {
            return $this->store->get($name);
        } catch (NotFoundException $nex) {
            throw new Twig_Error_Loader($nex->getMessage());
        } catch (UnavailableException $ex){
            throw new Twig_Error_Runtime($ex->getMessage());
        }
    }
}