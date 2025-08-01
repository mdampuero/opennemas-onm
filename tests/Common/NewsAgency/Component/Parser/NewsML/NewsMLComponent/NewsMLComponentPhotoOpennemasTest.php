<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent;

use Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentPhotoOpennemas;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

/**
 * Defines test cases for NewsMLComponentPhotoOpennemas class.
 */
class NewsMLComponentPhotoOpennemasTest extends TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->invalid = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->front   = simplexml_load_string($this->loadFixture('photo-opennemas-front.xml'));
        $this->inner   = simplexml_load_string($this->loadFixture('photo-opennemas-inner.xml'));

        $this->internalParser = $this->getMockBuilder('Common\NewsAgency\Component\Parser\Parser')
            ->disableOriginalConstructor()
            ->setMethods([ 'checkFormat', 'parse' ])
            ->getMock();

        $this->factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->factory->expects($this->any())->method('get')
            ->willReturn($this->internalParser);

        $this->parser = new NewsMLComponentPhotoOpennemas($this->factory, [
            'filename' => 'xyzzy fubar'
        ]);
    }

    /**
     * Tests checkFormat with valid and invalid XML.
     */
    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat(null));
        $this->assertFalse($this->parser->checkFormat($this->invalid));

        $xml = simplexml_load_string(
            $this->front->xpath('//NewsML/NewsItem/NewsComponent/NewsComponent')[1]->asXml()
        );

        $xml = simplexml_load_string(
            $xml->xpath('//NewsComponent')[1]->asXml()
        );

        $this->assertTrue($this->parser->checkFormat($xml));

        $xml = simplexml_load_string(
            $this->inner->xpath('//NewsML/NewsItem/NewsComponent/NewsComponent')[1]->asXml()
        );

        $xml = simplexml_load_string(
            $xml->xpath('//NewsComponent')[1]->asXml()
        );

        $this->assertTrue($this->parser->checkFormat($xml));
    }

    /**
     * Tests getFilename with valid XML.
     */
    public function testGetFilename()
    {
        $this->assertEquals('xyzzy fubar', $this->parser->getFilename($this->invalid));

        $xml = simplexml_load_string(
            $this->inner->xpath('//NewsML/NewsItem/NewsComponent/NewsComponent')[1]->asXml()
        );

        $xml = simplexml_load_string(
            $xml->xpath('//NewsComponent/NewsComponent/ContentItem')[0]->asXml()
        );

        $this->assertEquals(
            '2018100216271084045.jpg',
            $this->parser->getFilename($xml)
        );
    }

    /**
     * Tests getSummary with valid XML.
     */
    public function testGetSummary()
    {
        $xml = simplexml_load_string(
            $this->front->xpath('//NewsML/NewsItem/NewsComponent')[0]->asXml()
        );

        $this->assertContains(
            'millones de dispositivos',
            $this->parser->getSummary($xml)
        );
    }

    /**
     * Tests parse with valid XML.
     */
    public function testParse()
    {
        $xml = simplexml_load_string(
            $this->front->xpath('//NewsML/NewsItem/NewsComponent/NewsComponent')[1]->asXml()
        );

        $xml = simplexml_load_string(
            $xml->xpath('//NewsComponent')[1]->asXml()
        );

        $resource = $this->parser->parse($xml);

        $this->assertInstanceOf(
            'Common\NewsAgency\Component\Resource\ExternalResource',
            $resource
        );
        $this->assertEquals('2018100216271084045.jpg', $resource->file_name);
        $this->assertEquals('Opennemas', $resource->agency_name);
    }
}
