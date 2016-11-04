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
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithInvalidParameters()
    {
        $filter = $this->getMockBuilder('Common\Core\Component\Filter\Filter')
            ->setConstructorArgs([ null ])
            ->getMockForAbstractClass();
    }

    public function testGetEmptyParameter()
    {
        $filter = $this->getMockForAbstractClass('Common\Core\Component\Filter\Filter');

        $this->assertFalse($filter->getParameter('foo'));
    }

    public function testGetParameter()
    {
        $params = [ 'foo' => 'bar' ];

        $filter = $this->getMockBuilder('Common\Core\Component\Filter\Filter')
            ->setConstructorArgs([ $params ])
            ->getMockForAbstractClass();

        $this->assertEquals($params['foo'], $filter->getParameter('foo'));
    }
}
