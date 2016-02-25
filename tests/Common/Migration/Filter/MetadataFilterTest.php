<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\MetadataFilter;

class MetadataFilterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = new MetadataFilter();

        $this->filter->utils = \Mockery::mock('\Onm\StringUtils_' . uniqid());
    }

    public function testFilter()
    {
        $str = 'The string to convert';

        $this->filter->utils->shouldReceive('getTags')->once()->with($str);

        $this->filter->filter($str);
    }
}
