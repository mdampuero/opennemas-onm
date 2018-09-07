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

use Framework\Import\Parser\Nitf\NitfEfe;

class NitfEfeTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new NitfEfe($factory);

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
                    <rights.owner>Agencia EFE</rights.owner>
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

        $this->miss = simplexml_load_string("<nitf>
            <head>
                <meta name=\"prioridad\" content=\"320\" />
            </head>
        </nitf>");
    }

    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testGetPriority()
    {
        $this->assertEquals(5, $this->parser->getPriority($this->invalid));

        $this->assertEquals(4, $this->parser->getPriority($this->valid));

        $this->assertEquals(5, $this->parser->getPriority($this->miss));
    }
}
