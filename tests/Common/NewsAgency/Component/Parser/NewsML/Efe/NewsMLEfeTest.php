<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Parser\NewsML\Efe;

use Common\NewsAgency\Component\Parser\NewsML\Efe\NewsMLEfe;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Test\Core\TestCase;

/**
 * Defines test cases for class class.
 */
class NewsMLEfeTest extends TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->factory = $this->getMockBuilder('Common\NewsAgency\Component\Factory\ParserFactory')
            ->getMock();

        $this->invalid = simplexml_load_string($this->loadFixture('invalid.xml'));
        $this->valid   = simplexml_load_string($this->loadFixture('valid.xml'));

        $this->parser = $this->getMockBuilder('Common\NewsAgency\Component\Parser\NewsML\Efe\NewsMLEfe')
            ->setConstructorArgs([ $this->factory, [
                'agency_name' => 'bar agency',
                'body'        => '<p>wibble</p>',
                'tags'        => 'glork,fubar'
            ] ])->setMethods([ 'parseItem' ])
            ->getMock();
    }

    /**
     * Tests checkFormat with valid and invalid values.
     */
    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat(null));
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    /**
     * Tests getBody when body empty and not empty.
     */
    public function testGetAgencyName()
    {
        $this->assertEquals('bar agency', $this->parser->getAgencyName($this->invalid));
        $this->assertEquals('Agencia EFE', $this->parser->getAgencyName($this->valid));
    }

    /**
     * Tests getTags.
     */
    public function testGetTags()
    {
        $this->assertEquals(
            'glork,fubar',
            $this->parser->getTags($this->invalid)
        );

        $this->assertEquals(
            'EL,TIEMPO,PREDICCIÓN',
            $this->parser->getTags($this->valid)
        );
    }

    /**
     * Tests parse.
     */
    public function testParse()
    {
        $this->parser->expects($this->once())->method('parseItem')
            ->willReturn([ new ExternalResource() ]);

        $contents = $this->parser->parse($this->valid);

        $this->assertCount(1, $contents);
        $this->assertInstanceOf('Common\NewsAgency\Component\Resource\ExternalResource', $contents[0]);
        $this->assertEquals('Agencia EFE', $contents[0]->agency_name);
        $this->assertEquals('EL,TIEMPO,PREDICCIÓN', $contents[0]->tags);
    }
}
