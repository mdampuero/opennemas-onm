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

use Common\Core\Component\Filter\JoinFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for JoinFilter class.
 */
class JoinFilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'hasParameter' ])
            ->getMock();
    }

    /**
     * Tests filter when value is not an array.
     */
    public function testFilterWithNoArray()
    {
        $filter = new JoinFilter($this->container);
        $str    = 'foo';

        $this->assertEquals($str, $filter->filter($str));
    }

    /**
     * Tests filter with an array using default glue character.
     */
    public function testFilterWithArrayAndDefaultGlue()
    {
        $filter = new JoinFilter($this->container);
        $str    = [ 'foo', 'bar' ];

        $this->assertEquals('foo,bar', $filter->filter($str));
    }

    /**
     * Tests filter with an array using a custom glue character.
     */
    public function testFilterWithArrayAndGlue()
    {
        $str    = [ 'foo', 'bar' ];
        $params = [ 'glue' => '-' ];

        $filter = new JoinFilter($this->container, $params);

        $this->assertEquals('foo-bar', $filter->filter($str));
    }
}
