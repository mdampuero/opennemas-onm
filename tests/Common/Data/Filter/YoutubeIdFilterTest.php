<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Filter;

use Common\Data\Filter\YoutubeIdFilter;

/**
 * Defines test cases for YoutubeIdFilter class.
 */
class YoutubeIdFilterTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $this->filter = new YoutubeIdFilter($container);
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
