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

use Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentTextEuropaPress;
use Common\Test\Core\TestCase;

class NewsMLComponentTextEuropaPressTest extends TestCase
{
    public function setUp()
    {
        $this->invalid = simplexml_load_string('<foo></foo>');
        $this->valid   = simplexml_load_string($this->loadFixture('text-europa-press.xml'));

        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->parser = new NewsMLComponentTextEuropaPress($factory);
    }

    /**
     * Tests checkFormat with valid and invalid XML.
     */
    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat(null));
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertFalse($this->parser->checkFormat(
            simplexml_load_string($this->loadFixture('text-europa-press-invalid.xml'))
        ));

        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    /**
     * Tests getAgencyName with valid and invalid XML.
     */
    public function testGetAgencyName()
    {
        $this->assertEmpty($this->parser->getAgencyName($this->invalid));
        $this->assertEquals(
            'Europa Press',
            $this->parser->getAgencyName($this->valid)
        );
    }
}
