<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Factory;

use Common\NewsAgency\Component\Factory\ParserFactory;

class ParserFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods([ 'files', 'in', 'name', 'notName' ])
            ->getMock();

        $this->factory = new ParserFactory();

        $property = new \ReflectionProperty($this->factory, 'finder');
        $property->setAccessible(true);
        $property->setValue($this->factory, $this->finder);
    }

    /**
     * Tests constructor and checks if finder is valid.
     */
    public function testConstructor()
    {
        $factory  = new ParserFactory();
        $property = new \ReflectionProperty($factory, 'finder');
        $property->setAccessible(true);

        $this->assertInstanceOf(
            'Symfony\Component\Finder\Finder',
            $property->getValue($factory)
        );
    }

    /**
     * Tests get when a non-parseable XML element is provided as argument.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetWhenUnparseableXml()
    {
        $this->factory->get(simplexml_load_string('<foobar></foobar>'));
    }

    /**
     * Tests get when a parseable XML element is provided as argument.
     */
    public function testGetWhenParseableXml()
    {
        $this->assertInstanceOf(
            'Common\NewsAgency\Component\Parser\Parser',
            $this->factory->get(simplexml_load_string('<nitf></nitf>'))
        );
    }
}
