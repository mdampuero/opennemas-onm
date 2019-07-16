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

use Common\Data\Filter\UrlDecodeFilter;

/**
 * Defines test cases for UrlDecodeFilter class.
 */
class UrlDecodeFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')->getMock();

        $this->filter = new UrlDecodeFilter($this->container, []);
    }

    /**
     * Tests filter for multiple values.
     */
    public function testFilter()
    {
        $this->assertEmpty($this->filter->filter(''));
        $this->assertEquals('foobar frog', $this->filter->filter(urlencode('foobar frog')));
        $this->assertEquals('foobar frog', $this->filter->filter(urlencode(urlencode('foobar frog'))));
    }
}
