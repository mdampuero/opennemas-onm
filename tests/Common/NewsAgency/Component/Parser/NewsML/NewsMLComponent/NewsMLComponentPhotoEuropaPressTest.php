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

use Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent\NewsMLComponentPhotoEuropaPress;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

/**
 * Defines test cases for NewsMLComponentPhotoEuropaPress class.
 */
class NewsMLComponentPhotoEuropaPressTest extends TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->incomplete = simplexml_load_string($this->loadFixture('photo-incomplete.xml'));
        $this->invalid    = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->valid      = simplexml_load_string($this->loadFixture('photo-europa-press.xml'));

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

        $this->parser = new NewsMLComponentPhotoEuropaPress($this->factory, [
            'agency_name' => 'xyzzy fubar'
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

        $xml = simplexml_load_string(
            $this->valid->xpath('//NewsML/NewsItem/NewsComponent')[0]->asXml()
        );

        $this->parser->setBag([ 'agency_name' => 'Europa Press' ]);
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
            'EuropaPress',
            $this->parser->getAgencyName($xml)
        );
    }
}
