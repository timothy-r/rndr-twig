<?php

use Ace\TemplateFinder;

/**
 * @author timrodger
 * Date: 18/03/15
 */
class TemplateFinderTest extends PHPUnit_Framework_TestCase
{
    public function testFindTemplateReturnsFullPathWhenFound()
    {
        $finder = new TemplateFinder(__DIR__.'/../templates', 'twig');
        $template = $finder->find('hello');
        $this->assertNotNull($template);
    }

    public function testFindTemplateReturnsNullWhenNotFound()
    {
        $finder = new TemplateFinder(__DIR__.'/../templates', 'twig');
        $template = $finder->find('nope');
        $this->assertNull($template);
    }
}
