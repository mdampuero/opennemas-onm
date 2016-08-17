<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\SlugFilter;

class SlugFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterWithNoParameters()
    {
        $str = 'The string to convert';

        $filter = new SlugFilter();
        $filter->utils = \Mockery::mock('\Onm\StringUtils_' . uniqid());
        $filter->utils->shouldReceive('getTitle')->once()
            ->with($str, false, '.');

        $filter->filter($str);
    }

    public function testFilterWithParameters()
    {
        $str    = 'The string to convert';
        $params = [ 'separator' => '-', 'stopList' => false ];

        $filter = new SlugFilter($params);
        $filter->utils = \Mockery::mock('\Onm\StringUtils_' . uniqid());
        $filter->utils->shouldReceive('getTitle')->once()
            ->with($str, $params['stopList'], $params['separator']);

        $filter->filter($str, $params);
    }
}
