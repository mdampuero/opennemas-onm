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

use Common\NewsAgency\Component\Parser\Nitf\NitfOpennemas;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

class NitfOpennemasTest extends TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Common\NewsAgency\Component\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new NitfOpennemas($factory);

        $this->invalid = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->valid   = simplexml_load_string($this->loadFixture('valid-opennemas.xml'));
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
     * Tests getAgencyName with valid and invalid XML.
     */
    public function testGetAgencyName()
    {
        $this->assertEmpty($this->parser->getAgencyName($this->invalid));
        $this->assertEquals(
            'Opennemas',
            $this->parser->getAgencyName($this->valid)
        );
    }

    /**
     * Tests getBody with valid and invalid XML.
     */
    public function testGetBody()
    {
        $this->assertEmpty($this->parser->getBody($this->invalid));

        $body = $this->parser->getBody($this->valid);

        $this->assertContains('<p>Paragraph 1</p>', $body);
        $this->assertContains('<p>Paragraph 2</p>', $body);
    }

    /**
     * Tests getParentId with valid and invalid XML.
     */
    public function testGetParentId()
    {
        $this->assertEmpty($this->parser->getParentId($this->invalid));

        $this->assertEquals('26082021', $this->parser->getParentId($this->valid));
    }

    /**
     * Tests getTags with valid and invalid XML.
     */
    public function testGetTags()
    {
        $this->assertEmpty($this->parser->getTags($this->invalid));
        $this->assertEquals(
            'foo,bar,baz,foobar',
            $this->parser->getTags($this->valid)
        );
    }

    /**
     * Tests getUid with valid and invalid XML.
     */
    public function testGetUid()
    {
        $this->assertEmpty($this->parser->getUid($this->invalid));

        $this->assertEquals('20210907', $this->parser->getUid($this->valid));
    }

    /**
     * Tests parse with valid and invalid XML.
     */
    public function testParse()
    {
        $resource = $this->parser->parse($this->invalid);

        $this->assertEquals('text', $resource->type);
        $this->assertEquals(1, preg_match(
            '/urn:nitfopennemas::\d{14}:/',
            $resource->urn
        ));

        $resource = $this->parser->parse($this->valid);

        $this->assertEquals('Editorial', $resource->author);
        $this->assertEquals('author.png', $resource->author_photo);
    }

    /**
     * Tests getAuthor.
     */
    public function testGetAuthor()
    {
        $reflection = new \ReflectionClass('Common\NewsAgency\Component\Parser\Nitf\NitfOpennemas');
        $method     = $reflection->getMethod('getAuthor');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->parser, [ $this->invalid ]));
        $this->assertEquals(
            'Editorial',
            $method->invokeArgs($this->parser, [ $this->valid ])
        );
    }

    /**
     * Tests getAuthorPhoto.
     */
    public function testGetAuthorPhoto()
    {
        $reflection = new \ReflectionClass('Common\NewsAgency\Component\Parser\Nitf\NitfOpennemas');
        $method     = $reflection->getMethod('getAuthorPhoto');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->parser, [ $this->invalid ]));

        $this->assertEquals(
            'author.png',
            $method->invokeArgs($this->parser, [ $this->valid ])
        );
    }

    /**
     * Tests isOpennemasNitf for valid and invalid Opennemas NITF values.
     */
    public function testIsOpennemasNitf()
    {
        $reflection = new \ReflectionClass('Common\NewsAgency\Component\Parser\Nitf\NitfOpennemas');
        $method     = $reflection->getMethod('isOpennemasNitf');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->parser, [ $this->invalid ]));
        $this->assertTrue($method->invokeArgs($this->parser, [ $this->valid ]));
    }
}
