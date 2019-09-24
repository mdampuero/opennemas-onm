<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\Import\Parser\NewsML;

use Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentList;
use Common\NewsAgency\Component\Resource\ExternalResource;

class NewsMLComponentListTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->invalid = simplexml_load_string('<foo></foo>');
        $this->valid   = simplexml_load_string("<NewsComponent>
            <NewsComponent>
                <NewsComponent>
                </NewsComponent>
            </NewsComponent>
            <NewsComponent>
                <NewsComponent>
                </NewsComponent>
            </NewsComponent>
        </NewsComponent>");

        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $parser = $this->getMockBuilder('Common\NewsAgency\Component\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'getBag', 'parse' ])
            ->getMock();

        $parser->method('getBag')->willReturn([]);

        $photo = new ExternalResource();

        $photo->id   = 'photo1';
        $photo->type = 'photo';

        $parser->method('parse')->will(
            $this->onConsecutiveCalls(
                new ExternalResource(),
                [ $photo ]
            )
        );

        $factory->method('get')->willReturn($parser);

        $this->parser = new NewsMLComponentList($factory);
    }

    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testParse()
    {
        $this->assertEmpty($this->parser->parse($this->invalid));

        $resources = $this->parser->parse($this->valid);

        $this->assertEquals(2, count($resources));
        $this->assertEquals($resources[1]->id, $resources[0]->related[0]);
    }
}
