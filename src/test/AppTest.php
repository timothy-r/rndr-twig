<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Silex\WebTestCase;

/**
 * @author timrodger
 * Date: 18/03/15
 */
class AppTest extends WebTestCase
{
    private $templates = [];

    /**
     * bit of a clunky tear down...
     */
    public function tearDown()
    {
        foreach($this->templates as $template){
            unlink(__DIR__ .'/../templates/' . $template);
        }
        parent::tearDown();
    }

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

    public function testPostNestedJsonObjectToTemplateSuceeds()
    {
        $name = 'test';
        $email = 'trial@others.net';
        $template = 'User.name: {{ user.name }} User.email: {{ user.email }}';
        $data = json_encode(['user' => ['name' => $name, 'email' => $email]]);
        $client = $this->createClient();
        $client->request('PUT', '/complex.twig', [], [], ['CONTENT_TYPE' => 'text/twig'], $template);
        $this->templates []= 'complex.twig';
        $client->request('POST', '/complex', [], [], ['CONTENT_TYPE' => 'application/json'], $data);

        $this->assertResponseContents($client->getResponse(), 200, "User.name: $name User.email: $email");
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

    public function testPutAddsATemplate()
    {
        $name = 'fork';
        $body = 'A simple template with name: {{ name }}';
        $client = $this->createClient();
        $client->request('PUT', '/simple.twig', [], [], ['CONTENT_TYPE' => 'text/twig'], $body);
        $this->assertSame(201, $client->getResponse()->getStatusCode());

        $this->templates []= 'simple.twig';

        $client->request('POST', '/simple', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->assertResponseContents($client->getResponse(), 200, "A simple template with name: $name");
    }

    public function testPutReplacesATemplate()
    {
        $body = 'A simple template with name: {{ name }}';
        $client = $this->createClient();
        $client->request('PUT', '/simple.twig', [], [], ['CONTENT_TYPE' => 'text/twig'], $body);
        $this->assertSame(201, $client->getResponse()->getStatusCode());

        $this->templates []= 'simple.twig';

        $body = 'A new template with name: {{ name }}';
        $client->request('PUT', '/simple.twig', [], [], ['CONTENT_TYPE' => 'text/twig'], $body);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testPutAddsATemplateToASubDirectory()
    {
        $name = 'fork';
        $body = 'A simple template with name: {{ name }}';
        $client = $this->createClient();
        $client->request('PUT', '/module/sub-module/simple.twig', [], [], ['CONTENT_TYPE' => 'text/twig'], $body);
        $this->assertSame(201, $client->getResponse()->getStatusCode());
        $this->templates []= 'module/sub-module/simple.twig';
        $client->request('POST', '/module/sub-module/simple', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->assertResponseContents($client->getResponse(), 200, "A simple template with name: $name");
    }

    public function testHeadReturns404WhenTemplateDoesNotExist()
    {
        $client = $this->createClient();
        $client->request('HEAD', '/not-a-template.twig');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testHeadReturns200WhenTemplateDoesExist()
    {
        $body = 'A simple template with name: {{ name }}';
        $client = $this->createClient();
        $client->request('PUT', '/a-template.twig', [], [], ['CONTENT_TYPE' => 'text/twig'], $body);
        $this->templates []= 'a-template.twig';
        $client->request('HEAD', '/a-template.twig');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testCanGetRawTemplateContents()
    {
        $body = 'A simple template with name: {{ name }}';
        $client = $this->createClient();
        $client->request('PUT', '/a-template.twig', [], [], ['CONTENT_TYPE' => 'text/twig'], $body);
        $this->templates []= 'a-template.twig';
        $client->request('GET', '/a-template.twig');

        $this->assertResponseContents($client->getResponse(), 200, $body);
    }

    protected function assertResponseContents($response, $expected_status, $expected_body)
    {
        $this->assertSame($expected_status, $response->getStatusCode());
        $this->assertSame($expected_body, $response->getContent());
    }

    protected function assertTemplateWasRendered($client, $crawler, $name)
    {
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->filter("html:contains('Hello $name')")->count());
    }
}
