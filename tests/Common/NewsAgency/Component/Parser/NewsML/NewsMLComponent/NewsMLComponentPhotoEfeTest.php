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

use Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentPhotoEfe;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

/**
 * Defines test cases for NewsMLComponentPhotoEfe class.
 */
class NewsMLComponentPhotoEfeTest extends TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->invalid = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->valid   = simplexml_load_string($this->loadFixture('photo-efe.xml'));

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

        $this->parser = new NewsMLComponentPhotoEfe($this->factory, [
            'filename' => 'norf',
            'summary'  => 'At sapien. Aenean viverra justo ac sem'
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
            $this->valid->xpath('/NewsML/NewsItem/NewsComponent/NewsComponent')[1]->asXml()
        );

        $xml = simplexml_load_string(
            $xml->xpath('/NewsComponent/NewsComponent')[1]->asXml()
        );

        $this->assertFalse($this->parser->checkFormat($xml));

        $xml = simplexml_load_string(
            $this->valid->xpath('/NewsML/NewsItem/NewsComponent/NewsComponent')[1]->asXml()
        );

        $xml = simplexml_load_string(
            $xml->xpath('/NewsComponent/NewsComponent')[0]->asXml()
        );

        $this->assertTrue($this->parser->checkFormat($xml));
    }

    /**
     * Tests getFilename with valid and invalid XML.
     */
    public function testGetFilename()
    {
        $this->assertEquals('norf', $this->parser->getFilename($this->invalid));

        $xml = simplexml_load_string(
            $this->valid->xpath('//ContentItem')[3]->asXml()
        );

        $this->assertEquals(
            '20150923-11125521p.jpg',
            $this->parser->getFilename($xml)
        );
    }

    /**
     * Tests getSummary with valid and invalid XML.
     */
    public function testGetSummary()
    {
        $this->assertContains(
            'At sapien. Aenean viverra',
            $this->parser->getSummary($this->invalid)
        );

        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsComponent')[0]->asXml()
        );

        $this->assertContains(
            'El DAX 30 de Fráncfort',
            $this->parser->getSummary($xml)
        );
    }
}
