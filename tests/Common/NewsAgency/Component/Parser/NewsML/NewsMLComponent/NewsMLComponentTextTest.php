<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\NewsAgency\Component\Parser\NewsML\NewsMLComponent;

use Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentText;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

class NewsMLComponentTextTest extends TestCase
{
    public function setUp()
    {
        $this->incomplete = simplexml_load_string($this->loadFixture('incomplete.xml'));
        $this->invalid    = simplexml_load_string($this->loadFixture('text-invalid.xml'));
        $this->valid      = simplexml_load_string($this->loadFixture('text.xml'));

        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $parser = $this->getMockBuilder('Common\NewsAgency\Component\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'getBag', 'parse' ])
            ->getMock();

        $parser->method('getBag')->willReturn([]);

        $this->parser = new NewsMLComponentText($factory);
    }

    /**
     * Tests checkFormat with valid and invalid XML.
     */
    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat(null));
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertFalse($this->parser->checkFormat($this->incomplete));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    /**
     * Tests getBody with valid and invalid XML.
     */
    public function testGetBody()
    {
        $this->assertEmpty($this->parser->getBody($this->invalid));
        $this->assertEquals(
            '<p>Paragraph 1</p>',
            $this->parser->getBody($this->valid)
        );
    }

    /**
     * Tests parse with valid XML.
     */
    public function testParse()
    {
        $resource = $this->parser->parse($this->valid);

        $this->assertInstanceOf(
            'Common\NewsAgency\Component\Resource\ExternalResource',
            $resource
        );
        $this->assertEquals('<p>Paragraph 1</p>', $resource->body);
    }
}
