<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Parser\EuropaPress;

use Common\Test\Core\TestCase;
use Common\NewsAgency\Component\Parser\EuropaPress\EuropaPress;
use Common\NewsAgency\Component\Resource\ExternalResource;

class EuropaPressTest extends TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new EuropaPress($factory);

        $this->invalid = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->valid   = simplexml_load_string($this->loadFixture('valid.xml'));
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
     * Tests getBody with valid and invalid XML.
     */
    public function testGetBody()
    {
        $this->assertEmpty($this->parser->getBody($this->invalid));
        $this->assertEquals(
            '<p>Sample body</p>',
            $this->parser->getBody($this->valid)
        );
    }

    /**
     * Tests getCategory with valid and invalid XML.
     */
    public function testGetCategory()
    {
        $this->assertEmpty($this->parser->getCategory($this->invalid));
        $this->assertEquals('POL', $this->parser->getCategory($this->valid));
    }

    /**
     * Tests getCreated with valid and invalid XML.
     */
    public function testGetCreatedTime()
    {
        $date = new \DateTime('now');
        $this->assertTrue($date <= $this->parser->getCreatedTime($this->invalid));

        $date = \DateTime::createFromFormat('d/m/Y H:i:s', '21/09/2015 18:16:04', new \DateTimeZone('UTC'));
        $this->assertEquals($date, $this->parser->getCreatedTime($this->valid));
    }

    /**
     * Tests getId with valid and invalid XML.
     */
    public function testGetId()
    {
        $this->assertEmpty($this->parser->getId($this->invalid));

        $this->assertEquals('20150921181604', $this->parser->getId($this->valid));
    }

    /**
     * Tests getPhoto with valid and invalid XML.
     */
    public function testGetPhoto()
    {
        $this->assertEmpty($this->parser->getPhoto($this->invalid));

        $photo = $this->parser->getPhoto($this->valid);

        $this->assertInstanceOf(
            'Common\NewsAgency\Component\Resource\ExternalResource',
            $photo
        );
    }

    /**
     * Tests getPriority with valid and invalid XML and when priority map can
     * be applied.
     */
    public function testGetPriority()
    {
        $this->assertEquals(5, $this->parser->getPriority($this->invalid));
        $this->assertEquals(4, $this->parser->getPriority($this->valid));

        $this->assertEquals(2, $this->parser->getPriority(simplexml_load_string(
            $this->loadFixture('valid-specific-priority.xml')
        )));

        $this->assertEquals(5, $this->parser->getPriority(simplexml_load_string(
            $this->loadFixture('valid-high-priority.xml')
        )));
    }

    /**
     * Tests getSummary with valid and invalid XML.
     */
    public function testGetSummary()
    {
        $this->assertEmpty($this->parser->getSummary($this->invalid));
        $this->assertEquals(
            'Sample summary',
            $this->parser->getSummary($this->valid)
        );
    }

    /**
     * Tests getTitle with valid and invalid XML.
     */
    public function testGetTitle()
    {
        $this->assertEmpty($this->parser->getTitle($this->invalid));
        $this->assertEquals(
            'Sample title',
            $this->parser->getTitle($this->valid)
        );
    }

    /**
     * Tests getUrl with valid and invalid XML.
     */
    public function testGetUrn()
    {
        $this->assertEquals(1, preg_match(
            '/urn:europapress:europapress:\d{14}:/',
            $this->parser->getUrn($this->invalid)
        ));

        $this->assertEquals(
            'urn:europapress:europapress:20150921181604:text:20150921181604',
            $this->parser->getUrn($this->valid)
        );
    }

    public function testParse()
    {
        $resources = $this->parser->parse($this->valid);

        $this->assertCount(2, $resources);
        $this->assertEquals('EuropaPress', $resources[0]->agency_name);
        $this->assertEquals('text', $resources[0]->type);

        foreach ($resources as $resource) {
            $this->assertInstanceOf(
                'Common\NewsAgency\Component\Resource\ExternalResource',
                $resource
            );

            $this->assertEquals(1, preg_match(
                '/urn:europapress:europapress:\d{14}:/',
                $resource->urn
            ));
        }
    }
}
