<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Filter;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Filter class.
 */
class FilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'getParameter', 'hasParameter' ])
            ->getMock();
    }

    /**
     * Tests getParameter when invalid parameters provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithInvalidParameters()
    {
        $this->getMockBuilder('Common\Core\Component\Filter\Filter')
            ->setConstructorArgs([ $this->container, null ])
            ->getMockForAbstractClass();
    }

    /**
     * Tests getParameter when no parameter found.
     */
    public function testGetEmptyParameter()
    {
        $this->container->expects($this->once())->method('hasParameter')->willReturn(false);

        $filter = $this->getMockBuilder('Common\Core\Component\Filter\Filter')
            ->setConstructorArgs([ $this->container, [] ])
            ->getMockForAbstractClass();

        $this->assertFalse($filter->getParameter('foo'));
    }

    /**
     * Tests getParameter when parameter found in the array of parameters.
     */
    public function testGetParameterFromArguments()
    {
        $params = [ 'foo' => 'bar' ];

        $filter = $this->getMockBuilder('Common\Core\Component\Filter\Filter')
            ->setConstructorArgs([ $this->container, $params ])
            ->getMockForAbstractClass();

        $this->assertEquals($params['foo'], $filter->getParameter('foo'));
    }

    /**
     * Tests getParameter when parameter found in the service container.
     */
    public function testGetParameterFromServiceContainer()
    {
        $this->container->expects($this->once())->method('hasParameter')->willReturn(true);
        $this->container->expects($this->once())->method('getParameter')->with('glorp')->willReturn('plugh');

        $filter = $this->getMockBuilder('Common\Core\Component\Filter\Filter')
            ->setConstructorArgs([ $this->container, [] ])
            ->getMockForAbstractClass();

        $this->assertEquals('plugh', $filter->getParameter('glorp'));
    }
}
