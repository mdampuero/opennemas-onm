<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration;

use Common\Migration\Filter\BodyFilter;

class BodyFilterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = new BodyFilter();
    }

    public function testFilterWithEmptyLines()
    {
        $str      = "The string\nto\nparse\nwith\nempty lines";
        $expected = '<p>The string</p><p>to</p><p>parse</p><p>with</p><p>empty lines</p>';

        $this->assertEquals($expected, $this->filter->filter($str));
    }

    public function testFilterWithoutEmptyLines()
    {
        $str      = 'The string to parse with no empty lines';
        $expected = '<p>The string to parse with no empty lines</p>';

        $this->assertEquals($expected, $this->filter->filter($str));
    }
}
