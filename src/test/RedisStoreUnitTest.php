<?php namespace test;

use Ace\Store\Redis as RedisStore;
use Predis\Response\ServerException;

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

        $path = '/template';
        $content = 'A template body';
        $type = 'text/plain';
        $now = time();
        $this->mock_client->expects($this->once())
            ->method('hmset')
            ->with($path, 'content', $content, 'type', $type, 'last-modified', $now);

        $this->store->set($path, $content, $type);
    }

    /**
     * @expectedException \Ace\Store\UnavailableException
     */
    public function testSetTemplateThrowsExceptionOnError()
    {
        $this->givenAMockClient();
        $this->givenAStore();

        $path = '/template';
        $content = 'A template body';
        $type = 'text/plain';
        $now = time();
        $this->mock_client->expects($this->once())
            ->method('hmset')
            ->with($path, 'content', $content, 'type', $type, 'last-modified', $now)
            ->will($this->throwException(new ServerException()));

        $this->store->set($path, $content, $type);
    }

    public function testGetTemplate()
    {
        $this->givenAMockClient();
        $this->givenAStore();

        $path = '/template';
        $content = 'A template body';
        $type = 'text/plain';
        $now = time();

        $this->mock_client->expects($this->any())
            ->method('hmget')
            ->with($path)
            ->will($this->returnValue([$content, $type, $now]));

        $result = $this->store->get($path);

        $this->assertSame($content, $result['content']);
        $this->assertSame($type, $result['type']);
        $this->assertSame($now, $result['last-modified']);
    }

    /**
     * @expectedException \Ace\Store\NotFoundException
     */
    public function testGetTemplateThrowsExceptionWhenNotFound()
    {
        $this->givenAMockClient();
        $this->givenAStore();

        $path = '/template';

        $this->mock_client->expects($this->any())
            ->method('hmget')
            ->with($path)
            ->will($this->returnValue(null));

        $this->store->get($path);
    }

    /**
     * @expectedException \Ace\Store\UnavailableException
     */
    public function testGetTemplateThrowsExceptionsOnError()
    {
        $this->givenAMockClient();
        $this->givenAStore();

        $path = '/template';

        $this->mock_client->expects($this->any())
            ->method('hmget')
            ->with($path)
            ->will($this->throwException(new ServerException()));

        $this->store->get($path);
    }

    public function testDeleteRemovesATemplate()
    {
        $this->givenAMockClient();
        $this->givenAStore();
        $path = '/template';

        $this->mock_client->expects($this->once())
            ->method('del')
            ->with($path)
            ->will($this->returnValue(0));

        $result = $this->store->delete($path);
        $this->assertSame(0, $result);
    }

    /**
     * @expectedException \Ace\Store\UnavailableException
     */
    public function testDeleteThrowsAnExceptionOnError()
    {
        $this->givenAMockClient();
        $this->givenAStore();
        $path = '/template';

        $this->mock_client->expects($this->once())
            ->method('del')
            ->with($path)
            ->will($this->throwException(new ServerException()));

        $this->store->delete($path);
    }


    private function givenAMockClient()
    {
        $this->mock_client = $this->getMockBuilder('Predis\Client')
            ->setMethods(['hmset', 'hmget', 'del'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function givenAStore()
    {
        $this->store = new RedisStore($this->mock_client);
    }
}
