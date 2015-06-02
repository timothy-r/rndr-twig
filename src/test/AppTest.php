<?php

use Silex\WebTestCase;

/**
 * @author timrodger
 * Date: 18/03/15
 */
class AppTest extends WebTestCase
{
    private $client;

    public function createApplication()
    {
        putenv('REDIS_PORT=MEMORY');
        return require __DIR__.'/../app.php';
    }

    public function testPostToNonExistentTemplateFails()
    {
        $this->givenAClient();
        $this->client->request('POST', '/not/there/template');

        $this->thenTheResponseIs404();
    }

    public function testPostJsonToTemplateSuceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $body = json_encode(['name' => $name]);

        $this->givenAClient();
        $this->givenATemplateExists('/hello.twig', $template);

        $this->client->request('POST', '/hello.twig', [], [], ['CONTENT_TYPE' => 'application/json'], $body);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPostNestedJsonObjectToTemplateSucceeds()
    {
        $name = 'test';
        $email = 'trial@others.net';
        $template = 'User.name: {{ user.name }} User.email: {{ user.email }}';
        $body = json_encode(['user' => ['name' => $name, 'email' => $email]]);

        $this->givenAClient();
        $this->givenATemplateExists('/complex.twig', $template);

        $this->client->request('POST', '/complex.twig', [], [], ['CONTENT_TYPE' => 'application/json'], $body);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("User.name: $name User.email: $email");
    }

    public function testPostJsonToSubDirectoryTemplateSucceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $body = json_encode(['name' => $name]);
        $this->givenAClient();
        $this->givenATemplateExists('/sub/hi.twig', $template);

        $this->client->request('POST', '/sub/hi.twig', [], [], ['CONTENT_TYPE' => 'application/json'], $body);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPostFormToTemplateSucceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/hello.twig', $template);

        $this->client->request('POST', '/hello.twig', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPostMultiPartBodyToTemplateSucceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/hello.twig', $template);

        $this->client->request('POST', '/hello.twig', ['name' => $name], [], ['CONTENT_TYPE' => 'multipart/form-data']);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPostQueryParamsToTemplateSucceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/hello.twig', $template);

        $this->client->request('POST', "/hello.twig?name=$name");

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPutAddsATemplate()
    {
        $name = 'fork';
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/simple.twig', $template);

        $this->thenTheResponseIsSuccess();

        $this->client->request('POST', '/simple.twig', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("A simple template with name: $name");
    }

    public function testPutReplacesATemplate()
    {
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/simple.twig', $template);
        $this->thenTheResponseIsSuccess();

        $body = 'A new template with name: {{ name }}';
        $this->client->request('PUT', '/simple.twig', [], [], ['CONTENT_TYPE' => 'text/twig'], $body);
        $this->thenTheResponseIsSuccess();
    }

    public function testPutAddsATemplateToASubDirectory()
    {
        $name = 'fork';
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/module/sub-module/simple.twig', $template);

        $this->thenTheResponseIsSuccess();

        $this->client->request('POST', '/module/sub-module/simple.twig', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("A simple template with name: $name");
    }

    public function testHeadReturns404WhenTemplateDoesNotExist()
    {
        $this->givenAClient();
        $this->client->request('HEAD', '/not-a-template.twig');
        $this->thenTheResponseIs404();
    }

    public function testHeadReturns200WhenTemplateDoesExist()
    {
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/a-template.twig', $template);

        $this->client->request('HEAD', '/a-template.twig');

        $this->thenTheResponseIsSuccess();
    }

    public function testCanGetRawTemplateContents()
    {
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/a-template.twig', $template);

        $this->client->request('GET', '/a-template.twig');

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents($template);
    }

    public function testDeleteRemovesATemplate()
    {
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/a-template.twig', $template);

        $this->client->request('DELETE', '/a-template.twig');

        $this->thenTheResponseIsSuccess();

        $this->client->request('HEAD', '/a-template.twig');
        $this->thenTheResponseIs404();
    }

    public function testDeleteRespondsWithSuccessWhenTemplatesMissing()
    {
        $this->givenAClient();

        $this->client->request('HEAD', '/a-missing-template.twig');
        $this->thenTheResponseIs404();

        $this->client->request('DELETE', '/a-missing-template.twig');

        $this->thenTheResponseIsSuccess();
    }

    private function givenAClient()
    {
        $this->client = $this->createClient();
    }

    private function givenATemplateExists($name, $content)
    {
        $this->client->request('PUT', $name, [], [], ['CONTENT_TYPE' => 'text/twig'], $content);
    }

    private function thenTheResponseIsSuccess()
    {
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    private function thenTheResponseIs404()
    {
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    protected function assertResponseContents($expected_body)
    {
        $this->assertSame($expected_body, $this->client->getResponse()->getContent());
    }
}
