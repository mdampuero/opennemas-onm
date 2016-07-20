<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\YoutubeIdFilter;

class YoutubeIdFilterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = new YoutubeIdFilter();
    }

    public function testFilterWithNoYoutubeUrl()
    {
        $str = 'No youtube URL';

        $this->assertFalse($this->filter->filter($str));
    }

    public function testFilterWithYoutubeUrl()
    {
        $str = 'youtube.com/watch?v=si7QHFM1ZQc&feature=youtu.be';

        $this->assertEquals('si7QHFM1ZQc', $this->filter->filter($str));
    }
}
