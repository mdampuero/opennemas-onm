<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Parser\Nitf;

use Common\NewsAgency\Component\Parser\Nitf\NitfEfe;
use Common\Test\Core\TestCase;

class NitfEfeTest extends TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new NitfEfe($factory);

        $this->invalid = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->valid   = simplexml_load_string($this->loadFixture('valid-efe.xml'));
        $this->alt     = simplexml_load_string($this->loadFixture('valid-efe-priority.xml'));
        $this->high    = simplexml_load_string($this->loadFixture('valid-efe-high-priority.xml'));
    }

    /**
     * Tests checkFormat with valid and invalid XML.
     */
    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    /**
     * Tests getPriority with valid and invalid XML.
     */
    public function testGetPriority()
    {
        $this->assertEquals(5, $this->parser->getPriority($this->invalid));
        $this->assertEquals(4, $this->parser->getPriority($this->valid));
        $this->assertEquals(3, $this->parser->getPriority($this->alt));
        $this->assertEquals(5, $this->parser->getPriority($this->high));
    }
}
