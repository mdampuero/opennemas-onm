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

use Framework\Import\Parser\NewsML\NewsML;
use Framework\Import\Resource\Resource;

class NewsMLTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $parser = $this->getMockBuilder('Framework\Import\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'getBag', 'parse' ])
            ->getMock();

        $parser->method('getBag')->willReturn([]);
        $parser->method('parse')->willReturn(new Resource([ 'foo' => 'bar' ]));

        $factory->method('get')->willReturn($parser);

        $this->parser = new NewsML($factory);

        $this->invalid = simplexml_load_string('<foo></foo>');
        $this->valid   = simplexml_load_string("<NewsML>
            <NewsItem>
                <Identification>
                    <NewsIdentifier>
                        <ProviderId>afp.com</ProviderId>
                        <DateId>20040729</DateId>
                        <NewsItemId>040729054956.xm61wen7</NewsItemId>
                    </NewsIdentifier>
                </Identification>
                <AdministrativeMetadata>
                    <Provider>
                        <Party FormalName=\"Foobar Agency 'quote' \"/>
                    </Provider>
                </AdministrativeMetadata>
                <DescriptiveMetadata>
                    <OfInterestTo FormalName=\"sample,tags\"/>
                </DescriptiveMetadata>
                <NewsManagement>
                    <FirstCreated>20040729T054956Z</FirstCreated>
                    <Urgency FormalName=\"U\"></Urgency>
                </NewsManagement>
                <NewsComponent>
                    <NewsLines>
                        <HeadLine>Sample title</HeadLine>
                        <SubHeadLine>Sample pretitle</SubHeadLine>
                    </NewsLines>
                    <NewsComponent>
                        <MediaType FormalName=\"Text\" />
                        <ContentItem>
                            <p>Paragraph 1</p>
                            <p>Paragraph 2</p>
                        </ContentItem>
                    </NewsComponent>
                </NewsComponent>
            </NewsItem>
        </NewsML>");

        $this->miss = simplexml_load_string("<NewsML>
            <NewsItem>
                <NewsManagement>
                    <FirstCreated>20040729T054956Z</FirstCreated>
                    <Urgency FormalName=\"A\"></Urgency>
                </NewsManagement>
            </NewsItem>
        </NewsML>");
    }

    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testGetAgencyName()
    {
        $this->assertEmpty($this->parser->getAgencyName($this->invalid));

        $this->assertEquals(
            'Foobar Agency \'quote\' ',
            $this->parser->getAgencyName($this->valid)
        );
    }

    public function testGetBody()
    {
        $this->assertEmpty($this->parser->getBody($this->invalid));

        $this->assertEquals(
            '<p>Paragraph 1</p><p>Paragraph 2</p>',
            $this->parser->getBody($this->valid)
        );
    }

    public function testGetCreatedTime()
    {
        $date = new \DateTime('now');
        $this->assertTrue($date <= $this->parser->getCreatedTime($this->invalid));

        $date = \DateTime::createFromFormat('Ymd\THisP', '20040729T054956Z');
        $date->setTimezone(new \DateTimeZone('UTC'));

        $this->assertEquals($date, $this->parser->getCreatedTime($this->valid));
    }

    public function testGetId()
    {
        $this->assertNotEmpty($this->parser->getId($this->invalid));

        $this->assertEquals('040729054956.xm61wen7', $this->parser->getId($this->valid));
    }

    public function testGetPretitle()
    {
        $this->assertEmpty($this->parser->getPretitle($this->invalid));

        $this->assertEquals(
            'Sample pretitle',
            $this->parser->getPretitle($this->valid)
        );
    }

    public function testGetPriority()
    {
        $this->assertEquals(5, $this->parser->getPriority($this->invalid));

        $this->assertEquals(4, $this->parser->getPriority($this->valid));

        $this->assertEquals('A', $this->parser->getPriority($this->miss));
    }

    public function testGetTags()
    {
        $this->assertEmpty($this->parser->getTags($this->invalid));

        $this->assertEquals('sample,tags', $this->parser->getTags($this->valid));
    }

    public function testGetTitle()
    {
        $this->assertEmpty($this->parser->getTitle($this->invalid));

        $this->assertEquals(
            'Sample title',
            $this->parser->getTitle($this->valid)
        );
    }

    public function testGetType()
    {
        $this->assertEmpty($this->parser->getType($this->invalid));

        $this->assertEquals('Text', $this->parser->getType($this->valid));
    }

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

    public function testParse()
    {
        $date = \DateTime::createFromFormat('Ymd\THisP', '20040729T054956Z');
        $date->setTimezone(new \DateTimeZone('UTC'));

        $this->parser->parse($this->invalid);
        $bag = $this->parser->getBag();

        $this->assertEmpty($bag['agency_name']);
        $this->assertTrue($date->format('Y-m-d H:i:s') < $bag['created_time']);

        $this->parser->parse($this->valid);

        $this->assertEquals(
            [
                'agency_name'  => 'Foobar Agency \'quote\' ',
                'created_time' => $date->format('Y-m-d H:i:s'),
                'id'           => '040729054956.xm61wen7'
            ],
            $this->parser->getBag()
        );
    }
}
