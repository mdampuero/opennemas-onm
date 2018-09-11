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

use Common\Data\Filter\HtmlFilter;

/**
 * Defines test cases for HtmlFilter class.
 */
class HtmlFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $this->filter = new HtmlFilter($container);
    }

    /**
     * Tests filter when value contains HTML.
     */
    public function testFilterWithHtmlEntities()
    {
        $str      = '<p>The string</p><p>to</p><p>parse</p>';

        $expected = '&lt;p&gt;The string&lt;/p&gt;&lt;p&gt;to&lt;/p&gt;&lt;p&gt'
            . ';parse&lt;/p&gt;';

        $this->assertEquals($expected, $this->filter->filter($str));
    }

    /**
     * Tests filter when value does not contain HTML.
     */
    public function testFilterWithNoHtmlEntities()
    {
        $str = 'The string to parse';

        $this->assertEquals($str, $this->filter->filter($str));
    }
}
