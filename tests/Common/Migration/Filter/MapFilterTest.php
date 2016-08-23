<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\MapFilter;

class MapFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilterWithoutParameters()
    {
        new MapFilter();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilterWithoutInvalidMap()
    {
        new MapFilter([ 'map' => 'bar' ]);
    }

    public function testFilterWithInvalidString()
    {
        $params = [ 'map' => [ 'foo' => 'bar' ] ];
        $filter = new MapFilter($params);

        $this->assertFalse($filter->filter('xyz'));
    }

    public function testFilterWithValidMapAndString()
    {
        $params = [ 'map' => [ 'foo' => 'bar' ] ];
        $filter = new MapFilter($params);

        $this->assertEquals($params['map']['foo'], $filter->filter('foo'));
    }
}
