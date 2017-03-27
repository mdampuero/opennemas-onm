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

use Common\Core\Component\Filter\MapFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for MapFilter class.
 */
class MapFilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')->getMock();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilterWithoutParameters()
    {
        new MapFilter($this->container);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilterWithoutInvalidMap()
    {
        new MapFilter($this->container, [ 'map' => 'bar' ]);
    }

    public function testFilterWithInvalidString()
    {
        $params = [ 'map' => [ 'foo' => 'bar' ] ];
        $filter = new MapFilter($this->container, $params);

        $this->assertFalse($filter->filter('xyz'));
    }

    public function testFilterWithValidMapAndString()
    {
        $params = [ 'map' => [ 'foo' => 'bar' ] ];
        $filter = new MapFilter($this->container, $params);

        $this->assertEquals($params['map']['foo'], $filter->filter('foo'));
    }
}
