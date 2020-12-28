<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Parser\NewsML;

use Common\NewsAgency\Component\Parser\NewsML\NewsML;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

class NewsMLTest extends TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $parser = $this->getMockBuilder('Common\NewsAgency\Component\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'getBag', 'parse' ])
            ->getMock();

        $parser->method('getBag')->willReturn([ 'flob' => 'garply', 'norf' => 6843 ]);
        $parser->method('parse')->willReturn(new ExternalResource([ 'foo' => 'bar' ]));

        $factory->method('get')->willReturn($parser);

        $this->parser = new NewsML($factory, [ 'norf' => null ]);

        $this->invalid = simplexml_load_string('<foo></foo>');
        $this->valid   = simplexml_load_string($this->loadFixture('valid.xml'));
        $this->miss    = simplexml_load_string($this->loadFixture('incomplete.xml'));
    }

    /**
     * Tests checkFormat with valid and invalid XML.
     */
    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat(null));
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    /**
     * Tests getAgencyName with valid and invalid XML.
     */
    public function testGetAgencyName()
    {
        $this->assertEmpty($this->parser->getAgencyName($this->invalid));
        $this->assertEquals(
            'Foobar Agency \'quote\' ',
            $this->parser->getAgencyName($this->valid)
        );
    }

    /**
     * Tests getBody with valid and invalid XML.
     */
    public function testGetBody()
    {
        $this->assertEmpty($this->parser->getBody($this->invalid));
        $this->assertEquals(
            '<p>Paragraph 1</p><p>Paragraph 2</p>',
            $this->parser->getBody($this->valid)
        );
    }

    /**
     * Tests getCategory with valid and invalid XML.
     */
    public function testGetCategory()
    {
        $this->assertEmpty($this->parser->getCategory($this->invalid));
        $this->assertEquals(
            'POL',
            $this->parser->getCategory($this->valid)
        );
    }

    /**
     * Tests getCreatedTime with valid and invalid XML and when created_time is
     * defined in bag.
     */
    public function testGetCreatedTime()
    {
        $date = new \DateTime('now');
        $this->assertTrue($date <= $this->parser->getCreatedTime($this->invalid));

        $date = \DateTime::createFromFormat('Ymd\THisP', '20040729T054956Z');
        $date->setTimezone(new \DateTimeZone('UTC'));

        $this->assertEquals($date, $this->parser->getCreatedTime($this->valid));

        $this->parser->setBag([ 'created_time' => '2019-10-03 17:25:10' ]);

        $date = new \DateTime('2019-10-03 17:25:10');
        $this->assertEquals($date, $this->parser->getCreatedTime($this->invalid));
    }

    /**
     * Tests getCreatedTime with valid and invalid XML and when id is defined
     * in bag.
     */
    public function testGetId()
    {
        $this->assertNotEmpty($this->parser->getId($this->invalid));
        $this->assertEquals('040729054956.xm61wen7', $this->parser->getId($this->valid));

        $this->parser->setBag([ 'id' => 2981 ]);
        $this->assertEquals(2981, $this->parser->getId($this->invalid));
    }

    /**
     * Tests getPretitle with valid and invalid XML.
     */
    public function testGetPretitle()
    {
        $this->assertEmpty($this->parser->getPretitle($this->invalid));

        $this->assertEquals(
            'Sample pretitle',
            $this->parser->getPretitle($this->valid)
        );
    }

    /**
     * Tests getPriority with valid and invalid XML.
     */
    public function testGetPriority()
    {
        $this->assertEquals(5, $this->parser->getPriority($this->invalid));
        $this->assertEquals(4, $this->parser->getPriority($this->valid));
        $this->assertEquals('A', $this->parser->getPriority($this->miss));
    }

    /**
     * Tests getTags with valid and invalid XML.
     */
    public function testGetTags()
    {
        $this->assertEmpty($this->parser->getTags($this->invalid));
        $this->assertEquals('sample,tags', $this->parser->getTags($this->valid));
    }

    /**
     * Tests getTitle with valid and invalid XML.
     */
    public function testGetTitle()
    {
        $this->assertEmpty($this->parser->getTitle($this->invalid));
        $this->assertEquals(
            'Sample title',
            $this->parser->getTitle($this->valid)
        );
    }

    /**
     * Tests getType with valid and invalid XML.
     */
    public function testGetType()
    {
        $this->assertEmpty($this->parser->getType($this->invalid));
        $this->assertEquals('Text', $this->parser->getType($this->valid));
    }

    /**
     * Tests getUrn with valid and invalid XML.
     */
    public function testGetUrn()
    {
        $this->assertEquals(1, preg_match(
            '/urn:newsml::\d{14}:/',
            $this->parser->getUrn($this->invalid)
        ));

        $this->assertEquals(
            'urn:newsml:foobar-agency-quote:20040729054956:text:040729054956.xm61wen7',
            $this->parser->getUrn($this->valid)
        );
    }

    /**
     * Tests parse with valid and invalid XML.
     */
    public function testParse()
    {
        $date = \DateTime::createFromFormat('Ymd\THisP', '20040729T054956Z');
        $date->setTimezone(new \DateTimeZone('UTC'));

        $resources = $this->parser->parse($this->invalid);

        $this->assertEmpty($resources);

        $bag = $this->parser->getBag();

        $this->assertEmpty($bag['agency_name']);
        $this->assertTrue($date->format('Y-m-d H:i:s') < $bag['created_time']);

        $this->parser->parse($this->valid);

        $this->assertEquals([
            'agency_name'  => 'Foobar Agency \'quote\' ',
            'created_time' => $date->format('Y-m-d H:i:s'),
            'id'           => '040729054956.xm61wen7',
            'category'     => 'POL',
            'priority'     => 4,
            'flob'         => 'garply',
            'norf'         => 6843,
            'href'         => ''
        ], $this->parser->getBag());
    }
}
