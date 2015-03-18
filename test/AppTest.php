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
        return require __DIR__.'/../src/app.php';
    }

    public function testPostToNonExistentTemplateFails()
    {
        $client = $this->createClient();
        $client->request('POST', '/not/there/template.tpl');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
}