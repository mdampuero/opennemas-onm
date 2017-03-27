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

use Common\Core\Component\Filter\YoutubeUrlFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for YoutubeUrlFilter class.
 */
class YoutubeUrlFilterTest extends KernelTestCase
{
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $this->filter = new YoutubeUrlFilter($container);
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
