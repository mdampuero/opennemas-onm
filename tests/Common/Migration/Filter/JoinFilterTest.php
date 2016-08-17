<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\JoinFilter;

class JoinFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterWithNoArray()
    {
        $filter = new JoinFilter();
        $str    = 'foo';

        $this->assertEquals($str, $filter->filter($str));
    }

    public function testFilterWithArrayAndDefaultGlue()
    {
        $filter = new JoinFilter();
        $str    = [ 'foo', 'bar' ];

        $this->assertEquals('foo,bar', $filter->filter($str));
    }

    public function testFilterWithArrayAndGlue()
    {
        $str    = [ 'foo', 'bar' ];
        $params = [ 'glue' => '-' ];

        $filter = new JoinFilter($params);

        $this->assertEquals('foo-bar', $filter->filter($str));
    }
}
