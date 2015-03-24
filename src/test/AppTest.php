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
        $name = 'test';
        $body = json_encode(['name' => $name]);
        $client = $this->createClient();
        $crawler = $client->request('POST', '/hello', [], [], ['CONTENT_TYPE' => 'application/json'], $body);

        $this->assertTemplateWasRendered($client, $crawler, $name);
    }

    public function testPostJsonToSubDirectoryTemplateSucceeds()
    {
        $name = 'test';
        $body = json_encode(['name' => $name]);
        $client = $this->createClient();
        $crawler = $client->request('POST', '/sub/hi', [], [], ['CONTENT_TYPE' => 'application/json'], $body);

        $this->assertTemplateWasRendered($client, $crawler, $name);
    }

    public function testPostFormToTemplateSuceeds()
    {
        $name = 'test';
        $client = $this->createClient();
        $crawler = $client->request('POST', '/hello', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);
        $this->assertTemplateWasRendered($client, $crawler, $name);
    }

    public function testPostMultiPartBodyToTemplateSuceeds()
    {
        $name = 'test';
        $client = $this->createClient();
        $crawler = $client->request('POST', '/hello', ['name' => $name], [], ['CONTENT_TYPE' => 'multipart/form-data']);

        $this->assertTemplateWasRendered($client, $crawler, $name);
    }

    public function testPostQueryParamsToTemplateSuceeds()
    {
        $name = 'test';
        $client = $this->createClient();
        $crawler = $client->request('POST', "/hello?name=$name");

        $this->assertTemplateWasRendered($client, $crawler, $name);
    }

    protected function assertTemplateWasRendered($client, $crawler, $name)
    {
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->filter("html:contains('Hello $name')")->count());
    }
}
