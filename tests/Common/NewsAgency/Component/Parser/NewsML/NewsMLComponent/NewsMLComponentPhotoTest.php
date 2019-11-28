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

use Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentPhoto;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

/**
 * Defines test cases for NewsMLComponentPhoto class.
 */
class NewsMLComponentPhotoTest extends TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->invalid    = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->valid      = simplexml_load_string($this->loadFixture('photo.xml'));
        $this->incomplete = simplexml_load_string($this->loadFixture('photo-incomplete.xml'));

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

        $this->parser = new NewsMLComponentPhoto($this->factory, [
            'agency_name' => 'xyzzy fubar',
            'filename'    => 'norf',
            'height'      => 600,
            'id'          => 'thud',
            'title'       => 'At sapien. Aenean viverra justo ac sem',
            'url'         => 'http://waldo/fred',
            'width'       => 800
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
            $this->valid->xpath('//NewsML/NewsItem/NewsComponent')[0]->asXml()
        );

        $this->assertTrue($this->parser->checkFormat($xml));
    }

    /**
     * Tests getAgencyName with valid XML.
     */
    public function testGetAgencyName()
    {
        $this->assertContains(
            'xyzzy fubar',
            $this->parser->getAgencyName($this->invalid)
        );

        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsML/NewsItem/NewsComponent')[0]->asXml()
        );

        $this->assertContains(
            'AFP',
            $this->parser->getAgencyName($xml)
        );
    }

    /**
     * Tests getBody with valid XML.
     */
    public function testGetBody()
    {
        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsML/NewsItem/NewsComponent')[0]->asXml()
        );

        $this->assertContains(
            'Vartan et Guevork Tarloyan',
            $this->parser->getBody($xml)
        );
    }

    /**
     * Tests getContent with valid and invalid XML.
     */
    public function testGetContent()
    {
        $this->assertEmpty($this->parser->getContent($this->invalid));

        $xml = simplexml_load_string(
            $this->incomplete->xpath('//NewsML/NewsItem/NewsComponent')[0]->asXml()
        );

        $this->assertEmpty($this->parser->getContent($xml));

        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsML/NewsItem/NewsComponent')[0]->asXml()
        );

        $this->internalParser->expects($this->once())->method('parse')
            ->willReturn('baz bar fred');

        $this->assertEquals(
            'baz bar fred',
            $this->parser->getContent($xml)
        );
    }

    /**
     * Tests getFile with valid and invalid XML.
     */
    public function testGetFile()
    {
        $this->assertEmpty($this->parser->getFile($this->invalid));

        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsML/NewsItem/NewsComponent')[0]->asXml()
        );

        $this->assertContains(
            'SGE.SDI86.050701102222.photo00.default-384x256.jpg',
            $this->parser->getFile($xml)->asXml()
        );
    }

    /**
     * Tests getFilename with valid and invalid XML.
     */
    public function testGetFilename()
    {
        $this->assertEquals('norf', $this->parser->getFilename($this->invalid));

        $xml = simplexml_load_string(
            $this->valid->xpath('//ContentItem')[1]->asXml()
        );

        $this->assertEquals(
            'SGE.SDI86.050701102222.photo00.default-384x256.jpg',
            $this->parser->getFilename($xml)
        );
    }

    /**
     * Tests getHeight with valid and invalid XML.
     */
    public function testGetHeight()
    {
        $this->assertEquals(600, $this->parser->getHeight($this->invalid));

        $xml = simplexml_load_string(
            $this->valid->xpath('//ContentItem')[1]->asXml()
        );

        $this->assertEquals(256, $this->parser->getHeight($xml));
    }

    /**
     * Tests getId with valid and invalid XML.
     */
    public function testGetId()
    {
        $this->assertEquals('thud', $this->parser->getId($this->invalid));

        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsComponent')[0]->asXml()
        );

        $this->assertRegexp('/^[a-z0-9]{32}$/', $this->parser->getId($xml));
    }

    /**
     * Tests getSummary with valid and invalid XML.
     */
    public function testGetSummary()
    {
        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsComponent')[0]->asXml()
        );

        $this->assertContains(
            'Vartan et Guevork Tarloyan',
            $this->parser->getSummary($xml)
        );
    }

    /**
     * Tests getTitle with valid and invalid XML.
     */
    public function testGetTitle()
    {
        $this->assertEquals(
            'At sapien. Aenean viverra justo ac sem',
            $this->parser->getTitle($this->invalid)
        );

        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsComponent')[0]->asXml()
        );

        $this->assertContains(
            'Paris la semaine prochaine',
            $this->parser->getTitle($xml)
        );
    }

    /**
     * Tests getUrl with valid and invalid XML.
     */
    public function testGetUrl()
    {
        $this->assertEquals('http://waldo/fred', $this->parser->getUrl($this->invalid));

        $xml = simplexml_load_string(
            $this->valid->xpath('//ContentItem')[1]->asXml()
        );

        $this->assertContains(
            'SGE.SDI86.050701102222.photo00.default-384x256.jpg',
            $this->parser->getUrl($xml)
        );
    }

    /**
     * Tests getWidth with valid and invalid XML.
     */
    public function testGetWidth()
    {
        $this->assertEquals(800, $this->parser->getWidth($this->invalid));

        $xml = simplexml_load_string(
            $this->valid->xpath('//ContentItem')[1]->asXml()
        );

        $this->assertEquals(384, $this->parser->getWidth($xml));
    }

    /**
     * Tests parse.
     */
    public function testParse()
    {
        $parser = $this
            ->getMockBuilder('Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentPhoto')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContent' ])
            ->getMock();

        $parser->expects($this->once())->method('getContent')
            ->willReturn(new ExternalResource([ 'title' => 'wibble' ]));

        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsItem/NewsComponent')[0]->asXml()
        );

        $resource = $parser->parse($xml);

        $this->assertInstanceOf(
            'Common\NewsAgency\Component\Resource\ExternalResource',
            $resource
        );

        $this->assertEquals('image/jpg', $resource->image_type);
        $this->assertEquals('wibble', $resource->title);
        $this->assertContains('Vartan et Guevork Tarloyan', $resource->body);
        $this->assertEquals('photo', $resource->type);
        $this->assertEquals(
            'SGE.SDI86.050701102222.photo00.default-384x256.jpg',
            $resource->file_path
        );
    }
}
