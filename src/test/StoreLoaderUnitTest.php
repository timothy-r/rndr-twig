<?php namespace test;

use Ace\Store\UnavailableException;
use Ace\Twig\StoreLoader;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class StoreLoaderUnitTest extends \PHPUnit_Framework_TestCase
{
    private $mock_store;

    private $loader;

    public function testLoaderGetsTemplate()
    {
        $name = 'a-template-file.twig';
        $content = 'BODY';
        $this->givenAMockStore();
        $this->givenALoader();
        $this->givenATemplate($name, $content, '', time());

        $this->assertSame($content, $this->loader->getSource($name));
    }

    /**
     * @expectedException Twig_Error_Loader
     */
    public function testLoaderThrowsExceptionWhenTemplateIsMissing()
    {
        $name = 'a-template-file.twig';
        $this->givenAMockStore();
        $this->givenALoader();
        $this->givenAMissingTemplate($name);

        $this->loader->getSource($name);
    }

    public function testExistsReturnsTrueWhenStoreHasTemplate()
    {
        $name = 'a-template-file.twig';
        $content = 'BODY';
        $this->givenAMockStore();
        $this->givenALoader();
        $this->givenATemplate($name, $content, 'text/plain', time());

        $this->assertTrue($this->loader->exists($name));
    }

    public function testExistsReturnsFalseWhenStoreDoesNotHaveTemplate()
    {
        $name = 'a-template-file.twig';
        $this->givenAMockStore();
        $this->givenALoader();
        $this->givenAMissingTemplate($name);

        $this->assertFalse($this->loader->exists($name));
    }

    public function testIsFreshReturnsComparesLastModifiedProperty()
    {
        $name = 'a-template-file.twig';
        $content = 'BODY';
        $now = time();

        $this->givenAMockStore();
        $this->givenALoader();
        $this->givenATemplate($name, $content, 'text/plain', $now - 1000);

        $result = $this->loader->isFresh($name, $now);
        $this->assertTrue($result);
    }

    /**
     * @expectedException Twig_Error_Loader
     */
    public function testIsFreshThrowsExceptionWhenTemplateIsMissing()
    {
        $name = 'a-template-file.twig';
        $now = time();

        $this->givenAMockStore();
        $this->givenALoader();
        $this->givenAMissingTemplate($name);

        $this->loader->isFresh($name, $now);
    }

    public function testCacheKeyIsAHash()
    {
        $name = 'a-template-file.twig';
        $content = 'BODY';
        $now = time();

        $this->givenAMockStore();
        $this->givenALoader();
        $this->givenATemplate($name, $content, 'text/plain', $now - 1000);

        $cache_key = $this->loader->getCacheKey($name);
        $this->assertSame(md5($name), $cache_key);
    }

    /**
     * @expectedException Twig_Error_Loader
     */
    public function testGetCacheKeyThrowsExceptionWhenTemplateIsMissing()
    {
        $name = 'a-template-file.twig';

        $this->givenAMockStore();
        $this->givenALoader();
        $this->givenAMissingTemplate($name);

        $this->loader->getCacheKey($name);
    }

    private function givenATemplate($name, $content, $type = 'text/plain', $time)
    {
        $this->mock_store->expects($this->any())
            ->method('get')
            ->with($name)
            ->will($this->returnValue(['content' => $content, 'type' => $type, 'last-modified' => $time]));
    }

    private function givenAMissingTemplate($name)
    {
        $this->mock_store->expects($this->any())
            ->method('get')
            ->with($name)
            ->will($this->throwException(new UnavailableException()));
    }

    private function givenAMockStore()
    {
        $this->mock_store = $this->getMockBuilder('Ace\Store\StoreInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function givenALoader()
    {
        $this->loader = new StoreLoader($this->mock_store);
    }
}