<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\YoutubeUrlFilter;

class YoutubeUrlFilterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = new YoutubeUrlFilter();
    }

    public function testFilterWithId()
    {
        $str      = 'si7QHFM1ZQc';
        $expected = 'http://www.youtube.com/watch?v=' . $str;

        $this->assertEquals($expected, $this->filter->filter($str));
        $this->assertFalse($this->filter->filter(1));
        $this->assertFalse($this->filter->filter([]));
        $this->assertFalse($this->filter->filter(null));
    }
}
