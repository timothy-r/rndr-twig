<?php

use Ace\TemplateFinder;

/**
 * @author timrodger
 * Date: 18/03/15
 */
class TemplateFinderTest extends PHPUnit_Framework_TestCase
{
    public function testFindTemplate()
    {
        $finder = new TemplateFinder(__DIR__.'/../templates', 'twig');
        $funky = $finder->find('funky');
        $this->assertNotNull($funky);
    }
}
