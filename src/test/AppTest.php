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
        $this->givenATemplateExists('/hello', $template);

        $this->client->request('POST', '/hello', [], [], ['CONTENT_TYPE' => 'application/json'], $body);

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
        $this->givenATemplateExists('/complex', $template);

        $this->client->request('POST', '/complex', [], [], ['CONTENT_TYPE' => 'application/json'], $body);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("User.name: $name User.email: $email");
    }

    public function testPostJsonToSubDirectoryTemplateSucceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $body = json_encode(['name' => $name]);
        $this->givenAClient();
        $this->givenATemplateExists('/sub/hi', $template);

        $this->client->request('POST', '/sub/hi', [], [], ['CONTENT_TYPE' => 'application/json'], $body);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPostFormToTemplateSucceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/hello', $template);

        $this->client->request('POST', '/hello', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPostMultiPartBodyToTemplateSucceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/hello', $template);

        $this->client->request('POST', '/hello', ['name' => $name], [], ['CONTENT_TYPE' => 'multipart/form-data']);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPostQueryParamsToTemplateSucceeds()
    {
        $name = 'test';
        $template = 'Hello {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/hello', $template);

        $this->client->request('POST', "/hello?name=$name");

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("Hello $name");
    }

    public function testPutAddsATemplate()
    {
        $name = 'fork';
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/simple', $template);

        $this->thenTheResponseIsSuccess();

        $this->client->request('POST', '/simple', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("A simple template with name: $name");
    }

    public function testPutReplacesATemplate()
    {
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/simple', $template);
        $this->thenTheResponseIsSuccess();

        $body = 'A new template with name: {{ name }}';
        $this->client->request('PUT', '/simple', [], [], ['CONTENT_TYPE' => 'text/twig'], $body);
        $this->thenTheResponseIsSuccess();
    }

    public function testPutAddsATemplateToASubDirectory()
    {
        $name = 'fork';
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/module/sub-module/simple', $template);

        $this->thenTheResponseIsSuccess();

        $this->client->request('POST', '/module/sub-module/simple', ['name' => $name], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents("A simple template with name: $name");
    }

    public function testIncludingATemplate()
    {
        $this->givenAClient();

        $base = '<html><body>{% include "/html/navigation/menu" %}</body></html>';
        $this->givenATemplateExists('/html/pages/base', $base);

        $menu = '<ul><li>One</li><li>Toe</li></ul>';
        $this->givenATemplateExists('/html/navigation/menu', $menu);

        $this->client->request('POST', '/html/pages/base');
        $this->assertResponseContents("<html><body><ul><li>One</li><li>Toe</li></ul></body></html>");
    }

    public function testExtendingATemplate()
    {

    }

    public function testHeadReturns404WhenTemplateDoesNotExist()
    {
        $this->givenAClient();
        $this->client->request('HEAD', '/not-a-template');
        $this->thenTheResponseIs404();
    }

    public function testHeadReturns200WhenTemplateDoesExist()
    {
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/a-template', $template);

        $this->client->request('HEAD', '/a-template');

        $this->thenTheResponseIsSuccess();
    }

    public function testCanGetRawTemplateContents()
    {
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/a-template', $template);

        $this->client->request('GET', '/a-template');

        $this->thenTheResponseIsSuccess();
        $this->assertResponseContents($template);
    }

    public function testDeleteRemovesATemplate()
    {
        $template = 'A simple template with name: {{ name }}';
        $this->givenAClient();
        $this->givenATemplateExists('/a-template', $template);

        $this->client->request('DELETE', '/a-template');

        $this->thenTheResponseIsSuccess();

        $this->client->request('HEAD', '/a-template');
        $this->thenTheResponseIs404();
    }

    public function testDeleteRespondsWithSuccessWhenTemplatesMissing()
    {
        $this->givenAClient();

        $this->client->request('HEAD', '/a-missing-template');
        $this->thenTheResponseIs404();

        $this->client->request('DELETE', '/a-missing-template');

        $this->thenTheResponseIsSuccess();
    }

    public function testListRespondsWithArrayOfTemplates()
    {
        $this->givenAClient();

        $template_path_1 = '/path/to/a-template';
        $template = 'A simple template with name: {{ name }}';

        $this->givenATemplateExists($template_path_1, $template);

        $template_path_2 = '/path/to/another-template';
        $template = 'Another template with var: {{ me }}';

        $this->givenATemplateExists($template_path_2, $template);

        $this->client->request('GET', '/');

        $this->thenTheResponseIsSuccess();

        $this->assertResponseContents(json_encode([$template_path_1, $template_path_2], JSON_UNESCAPED_SLASHES));
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
