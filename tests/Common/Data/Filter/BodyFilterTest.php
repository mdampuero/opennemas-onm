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

use Common\Data\Filter\BodyFilter;

/**
 * Defines test cases for BodyFilter class.
 */
class BodyFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $this->filter = new BodyFilter($container);
    }

    /**
     * Tests filter when body contains empty lines.
     */
    public function testFilterWithEmptyLines()
    {
        $str      = "The string\nto\nparse\nwith\nempty lines";
        $expected = '<p>The string</p><p>to</p><p>parse</p><p>with</p><p>empty lines</p>';

        $this->assertEquals($expected, $this->filter->filter($str));
    }

    /**
     * Tests filter when body does not contain empty lines.
     */
    public function testFilterWithoutEmptyLines()
    {
        $str      = 'The string to parse with no empty lines';
        $expected = '<p>The string to parse with no empty lines</p>';

        $this->assertEquals($expected, $this->filter->filter($str));
    }
}
