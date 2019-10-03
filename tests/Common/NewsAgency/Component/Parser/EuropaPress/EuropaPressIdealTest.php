<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\Import\Parser\EuropaPress;

use Common\NewsAgency\Component\Parser\EuropaPress\EuropaPressIdeal;
use Common\NewsAgency\Component\Resource\ExternalResource;

class EuropaPressIdealTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $fixturesDir = str_replace('tests', 'fixtures', __DIR__);

        $this->invalid = simplexml_load_string(
            file_get_contents($fixturesDir . '/ideal-invalid.xml')
        );

        $this->valid = simplexml_load_string(
            file_get_contents($fixturesDir . '/ideal-valid.xml')
        );

        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new EuropaPressIdeal($factory);
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
     * Tests getsignature with valid and invalid XML.
     */
    public function testGetSignature()
    {
        $this->assertEmpty($this->parser->getSignature($this->invalid));
        $this->assertEquals('Sample signature not EP', $this->parser->getSignature($this->valid));
    }

    /**
     * Tests getPhotoFront with valid and invalid XML.
     */
    public function testGetPhotoFront()
    {
        $this->assertEmpty($this->parser->getPhotoFront($this->invalid));

        $photo = $this->parser->getPhotoFront($this->valid);

        $this->assertInstanceOf(
            'Common\NewsAgency\Component\Resource\ExternalResource',
            $photo
        );

        $this->assertEquals('20150921181604front_ig.photo', $photo->id);
        $this->assertEquals('Photo description', $photo->summary);
        $this->assertEquals('jpg', $photo->extension);
    }

    /**
     * Tests parse.
     */
    public function testParse()
    {
        $resources = $this->parser->parse($this->valid);

        $this->assertCount(3, $resources);
        $this->assertEquals('Grupo Idealgallego', $resources[0]->agency_name);
        $this->assertEquals('Grupo Idealgallego', $resources[0]->agency_name);
        $this->assertEquals('<p>Sample body</p>', $resources[0]->body);

        foreach ($resources as $resource) {
            $this->assertInstanceOf(
                'Common\NewsAgency\Component\Resource\ExternalResource',
                $resource
            );
        }
    }
}
