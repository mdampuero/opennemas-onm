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

use Framework\Import\Parser\NewsML\NewsMLComponent\NewsMLComponentTextEfe;
use Framework\Import\Resource\Resource;

class NewsMLComponentTextEfeTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->invalid = [
            simplexml_load_string("<foo></foo>"),
            simplexml_load_string("<NewsComponent>
                <ContentItem>
                    <MediaType FormalName=\"Text\"/>
                </ContentItem>
            </NewsComponent>")
        ];

        $this->valid   = simplexml_load_string("<NewsComponent>
            <ContentItem>
                <MediaType FormalName=\"Text\"/>
                <Format FormalName=\"NITF\"/>
                <DataContent>
                    <nitf>
                        <body>
                            <body.head>
                                <rights>
                                    <rights.owner>Agencia EFE</rights.owner>
                                </rights>
                            </body.head>
                        </body>
                    </nitf>
                  </DataContent>
            </ContentItem>
        </NewsComponent>");

        $this->resource = new Resource();

        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $parser = $this->getMockBuilder('Framework\Import\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'getBag', 'parse' ])
            ->getMock();

        $parser->method('parse')->willReturn($this->resource);
        $factory->method('get')->willReturn($parser);

        $this->parser = new NewsMLComponentTextEFE($factory);
    }

    public function testCheckFormat()
    {
        foreach ($this->invalid as $value) {
            $this->assertFalse($this->parser->checkFormat($value));
        }

        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testParse()
    {
        $this->assertEquals($this->resource, $this->parser->parse($this->valid));
    }
}
