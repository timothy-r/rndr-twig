<?php

use Silex\WebTestCase;

/**
 * @author timrodger
 * Date: 18/03/15
 */
class AppErrorTest extends WebTestCase
{
    private $client;

    public function createApplication()
    {
        putenv('REDIS_PORT=UNAVAILABLE');
        return require __DIR__.'/../app.php';
    }

    public function testPostReturnsErrorWhenUnavailable()
    {
        $this->givenAClient();
        $this->client->request('POST', '/not/there/template');

        $this->thenTheResponseIsError();
    }

    public function testPutReturnsErrorWhenUnavailable()
    {
        $this->givenAClient();

        $this->client->request('PUT', '/simple.twig', ['name' => 'name'], [], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);

        $this->thenTheResponseIsError();
    }

    public function testHeadReturnsErrorWhenUnavailable()
    {
        $this->givenAClient();
        $this->client->request('HEAD', '/not-a-template.twig');

        $this->thenTheResponseIsError();
    }

    public function testGetReturnsErrorWhenUnavailable()
    {
        $this->givenAClient();

        $this->client->request('GET', '/a-template.twig');

        $this->thenTheResponseIsError();
    }

    public function testDeleteReturnsErrorWhenUnavailable()
    {
        $this->givenAClient();

        $this->client->request('DELETE', '/a-template.twig');

        $this->thenTheResponseIsError();

    }

    private function givenAClient()
    {
        $this->client = $this->createClient();
    }

    private function thenTheResponseIs404()
    {
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    private function thenTheResponseIsError()
    {
        $this->assertSame(500, $this->client->getResponse()->getStatusCode());
    }

}
