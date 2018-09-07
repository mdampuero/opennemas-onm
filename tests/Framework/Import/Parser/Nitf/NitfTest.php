<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\Import\Parser\Nitf;

use Framework\Import\Parser\Nitf\Nitf;
use Framework\Import\Resource\Resource;

class NitfTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new Nitf($factory);

        $this->invalid = simplexml_load_string('<foo><nitf></nitf></foo>');
        $this->valid   = simplexml_load_string("<nitf>
            <head>
                <title>Sample title</title>
                <meta name=\"prioridad\" content=\"U\" />
                <docdata management-status=\"usable\">
                    <urgency ed-urg=\"3\" />
                    <evloc city=\"Budapest\" iso-cc=\"HUN\" />
                    <doc-id id-string=\"21155709\" />
                </docdata>
            </head>
            <body>
                <body.head>
                    <rights.owner>Foobar Agency</rights.owner>
                    <dateline>
                        <story.date norm=\"20150921T080200+0000\">
                            20150921T080200+0000
                        </story.date>
                    </dateline>
                    <abstract>
                        <p>Sample summary</p>
                    </abstract>
                </body.head>
                <body.content>
                    <p>Paragraph 1</p>
                    <p>Paragraph 2</p>
                </body.content>
            </body>
        </nitf>");
    }

    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testclean()
    {
        $cleaned = $this->parser->clean($this->invalid);
        $index   = strpos($cleaned->asXML(), "<?xml version=\"1.0\"?>\n<nitf");

        $this->assertEquals(0, $index);
    }

    public function testGetAgencyName()
    {
        $this->assertEmpty($this->parser->getAgencyName($this->invalid));

        $this->assertEquals(
            'Foobar Agency',
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


        $date = \DateTime::createFromFormat('Ymd\THisP', '20150921T080200+0000');
        $date->setTimezone(new \DateTimeZone('UTC'));

        $this->assertEquals($date, $this->parser->getCreatedTime($this->valid));
    }

    public function testGetId()
    {
        $this->assertEmpty($this->parser->getId($this->invalid));

        $this->assertEquals('21155709', $this->parser->getId($this->valid));
    }

    public function testGetPriority()
    {
        $this->assertEquals(5, $this->parser->getPriority($this->invalid));

        $this->assertEquals(3, $this->parser->getPriority($this->valid));
    }

    public function testGetSummary()
    {
        $this->assertEmpty($this->parser->getSummary($this->invalid));

        $this->assertEquals(
            '<p>Sample summary</p>',
            $this->parser->getSummary($this->valid)
        );
    }

    public function testGetTitle()
    {
        $this->assertEmpty($this->parser->getTitle($this->invalid));

        $this->assertEquals(
            'Sample title',
            $this->parser->getTitle($this->valid)
        );
    }

    public function testGetUrn()
    {
        $this->assertEquals(1, preg_match(
            '/urn:nitf::\d{14}:/',
            $this->parser->getUrn($this->invalid)
        ));

        $this->assertEquals(
            'urn:nitf:foobar_agency:20150921080200:text:21155709',
            $this->parser->getUrn($this->valid)
        );
    }

    public function testParse()
    {
        $parsed = $this->parser->parse($this->invalid);

        $this->assertEquals('text', $parsed->type);
        $this->assertEquals(1, preg_match(
            '/urn:nitf::\d{14}:/',
            $parsed->urn
        ));

        $resource = new Resource();
        $resource->agency_name = 'Foobar Agency';
        $resource->body = '<p>Paragraph 1</p><p>Paragraph 2</p>';

        $resource->created_time =
            \DateTime::createFromFormat('Ymd\THisP', '20150921T080200+0000');
        $resource->created_time->setTimezone(new \DateTimeZone('UTC'));

        $resource->created_time = $resource->created_time->format('Y-m-d H:i:s');

        $resource->id       = '21155709';
        $resource->priority = 3;
        $resource->summary  = '<p>Sample summary</p>';
        $resource->title    = 'Sample title';
        $resource->type     = 'text';
        $resource->urn      = 'urn:nitf:foobar_agency:20150921080200:text:21155709';

        $this->assertEquals($resource, $this->parser->parse($this->valid));
    }
}
