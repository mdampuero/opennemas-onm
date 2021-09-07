<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent;

use Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentList;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

class NewsMLComponentListTest extends TestCase
{
    public function setUp()
    {
        $this->invalid = simplexml_load_string('<foo></foo>');
        $this->valid   = simplexml_load_string($this->loadFixture('list.xml'));

        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $parser = $this->getMockBuilder('Common\NewsAgency\Component\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'getBag', 'parse' ])
            ->getMock();

        $factory->expects($this->any())->method('get')
            ->willReturn($parser);

        $parser->expects($this->any())->method('getBag')
            ->willReturn([]);
        $parser->method('parse')->will(
            $this->onConsecutiveCalls(new ExternalResource(), [
                new ExternalResource([ 'id' => 'photo1', 'type' => 'photo' ]),
                new ExternalResource([ 'id' => 'text2', 'type' => 'text', 'isChild' => true, 'uid' => '123' ]),
                new ExternalResource([ 'id' => 'photo2', 'type' => 'photo', 'isChild' => true, 'uid' => '123' ])
            ])
        );

        $this->parser = new NewsMLComponentList($factory);
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
     * Tests parse with valid and invalid XML.
     */
    public function testParse()
    {
        $this->assertEmpty($this->parser->parse($this->invalid));

        $resources = $this->parser->parse($this->valid);

        $this->assertEquals(4, count($resources));
        $this->assertEquals($resources[1]->id, $resources[0]->related[0]);
    }

    /**
     * Tests parse with valid XML and with child.
     */
    public function testParseWithChild()
    {
        $resources = $this->parser->parse($this->valid);

        $this->assertEquals(4, count($resources));
        $this->assertEquals($resources[1]->id, $resources[0]->related[0]);
    }
}
