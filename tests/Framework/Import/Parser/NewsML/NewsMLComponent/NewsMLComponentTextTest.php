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

use Framework\Import\Parser\NewsML\NewsMLComponent\NewsMLComponentText;
use Framework\Import\Resource\Resource;

class NewsMLComponentTextTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->invalid = simplexml_load_string('<foo></foo>');

        $this->miss   = simplexml_load_string("<NewsComponent>
            <ContentItem>
              <Format FormalName=\"bcNITF2.5\"/>
              <DataContent>
                <p>Paragraph 1</p>
              </DataContent>
            </ContentItem>
        </NewsComponent>");

        $this->valid   = simplexml_load_string("<NewsComponent>
            <ContentItem>
              <MediaType FormalName=\"Text\"/>
              <Format FormalName=\"bcNITF2.5\"/>
              <NewsItemId>040729054956.xm61wen7</NewsItemId>
              <DataContent>
                <p>Paragraph 1</p>
              </DataContent>
            </ContentItem>
        </NewsComponent>");

        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $parser = $this->getMockBuilder('Framework\Import\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'getBag', 'parse' ])
            ->getMock();

        $parser->method('getBag')->willReturn([]);

        $this->parser = new NewsMLComponentText($factory);
    }

    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertFalse($this->parser->checkFormat($this->miss));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testParse()
    {
        $urn = 'urn:newsmlcomponenttext::'.date('YmdHis').
               ':text:040729054956.xm61wen7';

        $resource = new Resource([
            'body' => '<p>Paragraph 1</p>',
            'urn'  => $urn,
            'type' => 'text'
        ]);

        $this->assertEquals($resource, $this->parser->parse($this->valid));
    }
}
