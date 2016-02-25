<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\DateFilter;

class DateFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterWithoutParams()
    {
        $str      = '10 September 2000';
        $expected = '2000-09-10 00:00:00';

        $filter = new DateFilter();

        $this->assertEquals($expected, $filter->filter($str));
    }

    public function testFilterWithParamsFromString()
    {
        $params = [ 'format' => 'Y-m-d' ];
        $str      = '10 September 2000';
        $expected = '2000-09-10';

        $filter = new DateFilter($params);

        $this->assertEquals($expected, $filter->filter($str));
    }

    public function testFilterWithParamsFromTimestamp()
    {
        $params = [ 'format' => 'H:i:s d/m/Y', 'timestamp' => true ];
        $str      = 1208169872;
        $expected = '12:44:32 14/04/2008';

        $filter = new DateFilter($params);

        $this->assertEquals($expected, $filter->filter($str));
    }
}
