<?php namespace test;

use Ace\Store\Redis as RedisStore;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class RedisStoreUnitTest extends \PHPUnit_Framework_TestCase
{
    private $mock_client;

    private $store;

    public function testSetTemplate()
    {
        $this->givenAMockClient();
        $this->givenAStore();

        $path = '/template.twig';
        $content = 'A template body';
        $type = 'text/plain';
        $now = time();
        $this->mock_client->expects($this->once())
            ->method('hmset')
            ->with($path, 'content', $content, 'last-modified', $now, 'type', $type);

        $this->store->set($path, $content, $type);
    }

    private function givenAMockClient()
    {
        $this->mock_client = $this->getMockBuilder('Predis\Client')
            ->setMethods(['hmset'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function givenAStore()
    {
        $this->store = new RedisStore($this->mock_client);
    }
}