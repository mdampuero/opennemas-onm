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

use Common\NewsAgency\Component\Parser\Nitf\Nitf;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

class NitfTest extends TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new Nitf($factory);

        $this->invalid  = simplexml_load_string('<foo><nitf></nitf></foo>');
        $this->valid    = simplexml_load_string($this->loadFixture('valid.xml'));
        $this->validAlt = simplexml_load_string($this->loadFixture('valid-alternative-id.xml'));
    }

    /**
     * Tests checkFormat with valid and invalid XML.
     */
    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat(null));
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    /**
     * Tests clean.
     */
    public function testClean()
    {
        $cleaned = $this->parser->clean($this->invalid);
        $index   = strpos($cleaned->asXML(), "<?xml version=\"1.0\"?>\n<nitf");

        $this->assertEquals(0, $index);
    }

    /**
     * Tests getAgencyName.
     */
    public function testGetAgencyName()
    {
        $this->assertEmpty($this->parser->getAgencyName($this->invalid));
        $this->assertEquals(
            'Foobar Agency',
            $this->parser->getAgencyName($this->valid)
        );
    }

    /**
     * Tests getBody with valid and invalid XML.
     */
    public function testGetBody()
    {
        $this->assertEmpty($this->parser->getBody($this->invalid));

        $this->assertEquals(
            '<p>Paragraph 1</p><p>Paragraph 2</p>',
            $this->parser->getBody($this->valid)
        );

        $this->assertContains('Paragraph 1', $this->parser->getBody($this->validAlt));
        $this->assertContains('Paragraph 2', $this->parser->getBody($this->validAlt));
        $this->assertContains('<br>', $this->parser->getBody($this->validAlt));
    }

    /**
     * Tests getCategory with valid and invalid XML.
     */
    public function testGetCategory()
    {
        $this->assertEmpty($this->parser->getCategory($this->invalid));

        $this->assertEquals(
            'POL',
            $this->parser->getCategory($this->valid)
        );
    }

    /**
     * Tests getCreatedTime with valid and invalid XML.
     */
    public function testGetCreatedTime()
    {
        $date = new \DateTime('now');
        $this->assertTrue($date <= $this->parser->getCreatedTime($this->invalid));


        $date = \DateTime::createFromFormat('Ymd\THisP', '20150921T080200+0000');
        $date->setTimezone(new \DateTimeZone('UTC'));

        $this->assertEquals($date, $this->parser->getCreatedTime($this->valid));
        $this->assertEquals($date, $this->parser->getCreatedTime($this->validAlt));
    }

    /**
     * Tests getId with valid and invalid XML and when id is provided as
     * attribute or as content.
     */
    public function testGetId()
    {
        $this->assertEmpty($this->parser->getId($this->invalid));

        $this->assertEquals('21155709', $this->parser->getId($this->valid));
        $this->assertEquals('30257', $this->parser->getId($this->validAlt));
    }

    /**
     * Tests getPriority with valid and invalid XML.
     */
    public function testGetPriority()
    {
        $this->assertEquals(5, $this->parser->getPriority($this->invalid));
        $this->assertEquals(3, $this->parser->getPriority($this->valid));

        $this->parser->setBag([ 'priority' => 2 ]);
        $this->assertEquals(2, $this->parser->getPriority($this->invalid));
    }

    /**
     * Tests getSummary with valid and invalid XML.
     */
    public function testGetSummary()
    {
        $this->assertEmpty($this->parser->getSummary($this->invalid));

        $this->assertEquals(
            '<p>Sample summary</p>',
            $this->parser->getSummary($this->valid)
        );

        $this->assertEquals(
            'Sample summary',
            $this->parser->getSummary($this->validAlt)
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
     * Tests getUrn with valid and invalid XML.
     */
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

    /**
     * Tests parse with valid and invalid XML.
     */
    public function testParse()
    {
        $parsed = $this->parser->parse($this->invalid);

        $this->assertEquals('text', $parsed->type);
        $this->assertEquals(1, preg_match(
            '/urn:nitf::\d{14}:/',
            $parsed->urn
        ));

        $resource = $this->parser->parse($this->valid);

        $this->assertInstanceOf(
            'Common\NewsAgency\Component\Resource\ExternalResource',
            $resource
        );

        $this->assertEquals(3, $resource->priority);
        $this->assertEquals('21155709', $resource->id);
        $this->assertEquals('POL', $resource->category);
        $this->assertEquals('Sample title', $resource->title);
        $this->assertEquals('urn:nitf:foobar_agency:20150921080200:text:21155709', $resource->urn);
    }
}
