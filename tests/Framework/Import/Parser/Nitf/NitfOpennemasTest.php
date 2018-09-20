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

use Framework\Import\Parser\Nitf\NitfOpennemas;
use Framework\Import\Resource\Resource;

class NitfOpennemasTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new NitfOpennemas($factory);

        $this->invalid = simplexml_load_string('<foo><nitf></nitf></foo>');
        $this->valid   = simplexml_load_string("<nitf>
            <head>
                <title>Sample title</title>
                <docdata management-status=\"usable\">
                    <doc.rights provider=\"Opennemas\"/>
                    <doc-id id-string=\"21155709\" />
                    <key-list>
                        <keyword key=\"foo,bar,baz,foobar\"/>
                    </key-list>
                </docdata>
            </head>
            <body>
                <body.head>
                    <hedline>
                      <hl1>Headline1</hl1>
                      <hl2>Headline2</hl2>
                    </hedline>
                    <rights>
                      <rights.owner>{&quot;name&quot;:&quot;Editorial&quot;}</rights.owner>
                      <rights.owner.photo>author.png</rights.owner.photo>
                    </rights>
                    <dateline>
                        <story.date norm=\"20150921T080200+0000\">
                            20150921T080200+0000
                        </story.date>
                    </dateline>
                    <abstract>
                        <p>Sample summary</p>
                    </abstract>
                </body.head>
                <body.content>"
                . "&amp;lt;p&amp;gt;Paragraph 1&amp;lt;/p&amp;gt;"
                . "&amp;lt;p&amp;gt;Paragraph 2&amp;lt;/p&amp;gt;"
                . "</body.content>
            </body>
        </nitf>");
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
            'Opennemas',
            $this->parser->getAgencyName($this->valid)
        );
    }

    public function testGetTags()
    {
        $this->assertEmpty($this->parser->getTags($this->invalid));

        $this->assertEquals(
            'foo,bar,baz,foobar',
            $this->parser->getTags($this->valid)
        );
    }

    public function testParse()
    {
        $parsed = $this->parser->parse($this->invalid);

        $author        = new \StdClass();
        $author->name  = 'Editorial';
        $author->photo = 'author.png';

        $resource               = new Resource();
        $resource->agency_name  = 'Opennemas';
        $resource->body         = '<p>Paragraph 1</p><p>Paragraph 2</p>';
        $resource->id           = '21155709';
        $resource->author       = $author;
        $resource->summary      = '<p>Sample summary</p>';
        $resource->title        = 'Sample title';
        $resource->priority     = 5;
        $resource->type         = 'text';
        $resource->tags         = 'foo,bar,baz,foobar';
        $resource->urn          = 'urn:nitfopennemas:opennemas:20150921080200:text:21155709';
        $resource->created_time =
            \DateTime::createFromFormat('Ymd\THisP', '20150921T080200+0000');
        $resource->created_time->setTimezone(new \DateTimeZone('UTC'));
        $resource->created_time = $resource->created_time->format('Y-m-d H:i:s');

        $this->assertEquals($resource, $this->parser->parse($this->valid));
    }

    public function testGetBody()
    {
        $this->assertEmpty($this->parser->getBody($this->invalid));

        $this->assertEquals(
            '<p>Paragraph 1</p><p>Paragraph 2</p>',
            $this->parser->getBody($this->valid)
        );
    }

    public function testGetAuthor()
    {
        $reflection = new \ReflectionClass('Framework\Import\Parser\Nitf\NitfOpennemas');
        $method     = $reflection->getMethod('getAuthor');
        $method->setAccessible(true);

        $author        = new \StdClass();
        $author->name  = 'Editorial';
        $author->photo = 'author.png';

        $criteria = $method->invokeArgs($this->parser, [ $this->invalid ]);
        $this->assertEmpty($criteria);

        $criteria = $method->invokeArgs($this->parser, [ $this->valid ]);
        $this->assertEquals($author, $criteria);
    }

    public function testGetAuthorPhoto()
    {
        $reflection = new \ReflectionClass('Framework\Import\Parser\Nitf\NitfOpennemas');
        $method     = $reflection->getMethod('getAuthorPhoto');
        $method->setAccessible(true);

        $criteria = $method->invokeArgs($this->parser, [ $this->invalid ]);
        $this->assertEmpty($criteria);

        $criteria = $method->invokeArgs($this->parser, [ $this->valid ]);
        $this->assertEquals('author.png', $criteria);
    }
}
