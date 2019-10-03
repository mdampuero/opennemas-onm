<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Parser\NewsML\EuropaPress;

use Common\NewsAgency\Component\Parser\NewsML\EuropaPress\NewsMLEuropaPress;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

/**
 * Defines test cases for class class.
 */
class NewsMLEuropaPressTest extends TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $fixturesDir = str_replace('tests', 'fixtures', __DIR__);

        $this->factory = $this->getMockBuilder('Common\NewsAgency\Component\Factory\ParserFactory')
            ->getMock();

        $this->invalid = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->valid   = simplexml_load_string($this->loadFixture('valid.xml'));

        $this->parser = $this->getMockBuilder('Common\NewsAgency\Component\Parser\NewsML\EuropaPress\NewsMLEuropaPress')
            ->setConstructorArgs([ $this->factory, [
                'body' => '<p>wibble</p>'
            ] ])->setMethods([ 'parseItem' ])
            ->getMock();
    }

    /**
     * Tests checkFormat with valid and invalid values.
     */
    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat(null));
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    /**
     * Tests getBody when body empty and not empty.
     */
    public function testGetBody()
    {
        $this->assertEquals('<p>wibble</p>', $this->parser->getBody($this->invalid));
        $this->assertContains('Control de la Calidad', $this->parser->getBody($this->valid));
    }

    /**
     * Tests getCategory when category can and can not be extracted from XML.
     */
    public function testGetCategory()
    {
        $this->assertEmpty($this->parser->getCategory($this->invalid));
        $this->assertEquals('CYS', $this->parser->getCategory($this->valid));
    }

    /**
     * Tests getCreatedTime.
     */
    public function testGetCreatedTime()
    {
        $date = new \DateTime('2019-10-02 16:21:30');
        $date->setTimeZone(new \DateTimeZone('UTC'));

        $this->assertEquals($date, $this->parser->getCreatedTime($this->valid));
    }

    /**
     * Tests getTags.
     */
    public function testGetTags()
    {
        $this->assertEquals('Sociedad,Social Issues', $this->parser->getTags($this->valid));
    }

    /**
     * Tests parse.
     */
    public function testParse()
    {
        $this->parser->expects($this->once())->method('parseItem')
            ->willReturn([ new ExternalResource() ]);

        $contents = $this->parser->parse($this->valid);

        $this->assertCount(1, $contents);
        $this->assertInstanceOf('Common\NewsAgency\Component\Resource\ExternalResource', $contents[0]);
        $this->assertEquals('CYS', $contents[0]->category);
        $this->assertEquals('Sociedad,Social Issues', $contents[0]->tags);
    }
}
