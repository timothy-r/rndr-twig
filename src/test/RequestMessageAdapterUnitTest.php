<?php namespace test; 

use Ace\Request\MessageAdapter as RequestMessage;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author timrodger
 * Date: 29/03/15
 */
class RequestMessageAdapterUnitTest extends \PHPUnit_Framework_TestCase
{
    private $request;

    private $message;

    public function testGetJsonData()
    {
        $data = ['name' => 'a test user'];
        $this->givenARequest('application/json', [], [], json_encode($data));
        $this->givenAMessage();
        $this->assertDataEquals($data);
    }

    public function testGetJsonDataReturnsEmptyArrayForBrokenJson()
    {
        $this->givenARequest('application/json', [], [], 'not a json string');
        $this->givenAMessage();
        $this->assertDataEquals([]);
    }

    public function testGetFormUrlEncodedData()
    {
        $data = ['name' => 'a test user'];
        $this->givenARequest('application/x-www-form-urlencoded', [], $data, '');
        $this->givenAMessage();
        $this->assertDataEquals($data);
    }

    public function testGetMultiPartFormData()
    {
        $data = ['name' => 'a test user'];
        $this->givenARequest('multipart/form-data', [], $data, '');
        $this->givenAMessage();
        $this->assertDataEquals($data);
    }

    public function testGetQueryData()
    {
        $data = ['name' => 'a test user'];
        $this->givenARequest('text/plain', $data, [], '');
        $this->givenAMessage();
        $this->assertDataEquals($data);
    }

    private function givenARequest($content_type, array $query = [], array $request = [], $content = '')
    {
        $server = ['CONTENT_TYPE' => $content_type];
        $this->request = new Request($query, $request, [], [], [], $server, $content);
    }

    private function givenAMessage()
    {
        $this->message = new RequestMessage($this->request);
    }

    private function assertDataEquals($data)
    {
        $result = $this->message->getData();
        $this->assertSame($data, $result);
    }
}