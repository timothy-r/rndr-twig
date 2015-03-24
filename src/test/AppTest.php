<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Silex\WebTestCase;

/**
 * @author timrodger
 * Date: 18/03/15
 */
class AppTest extends WebTestCase
{
    public function testGetIndexReturnsJson()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $client->getResponse()->headers->get('Content-Type'));
    }

    public function createApplication()
    {
        return require __DIR__.'/../app.php';
    }

    public function testPostToNonExistentTemplateFails()
    {
        $client = $this->createClient();
        $client->request('POST', '/not/there/template');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testPostJsonToTemplateSuceeds()
    {
        $body = json_encode(['name' => 'test']);
        $client = $this->createClient();
        $client->request('POST', '/hello', [], [], ['CONTENT_TYPE' => 'application/json'], $body);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testPostJsonToSubDirectoryTemplateSucceeds()
    {
        $body = json_encode(['name' => 'test']);
        $client = $this->createClient();
        $client->request('POST', '/sub/hi', [], [], ['CONTENT_TYPE' => 'application/json'], $body);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testPostFormToTemplateSuceeds()
    {
        $body = ['name' => 'test'];
        $client = $this->createClient();
        $client->request('POST', '/hello', [], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded'], $body);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testPostMultiPartBodyToTemplateSuceeds()
    {
        $body = ['name' => 'test'];
        $client = $this->createClient();
        $client->request('POST', '/hello', [], [], ['CONTENT_TYPE' => 'multipart/form-data'], $body);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
