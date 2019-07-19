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

use Common\Data\Filter\NoHtmlFilter;

/**
 * Defines test cases for HtmlFilter class.
 */
class NoHtmlFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $this->filter = new NoHtmlFilter($container);
    }

    /**
     * Tests filter when the string contains HTML tags and entities.
     */
    public function testFilterWithHtml()
    {
        $str = '<p>&lsquo;The string to parse&rsquo;</p>';

        $this->assertEquals('‘The string to parse’', $this->filter->filter($str));
    }

    /**
     * Tests filter when the string contains HTML entities.
     */
    public function testFilterWithHtmlEntities()
    {
        $str = '&lsquo;The string to parse&rsquo;';

        $this->assertEquals('‘The string to parse’', $this->filter->filter($str));
    }

    /**
     * Tests filter when the string contains HTML tags.
     */
    public function testFilterWithHtmlTags()
    {
        $str = '<p>The string to parse</p>';

        $this->assertEquals('The string to parse', $this->filter->filter($str));
    }

    /**
     * Tests filter when the string does not contain HTML.
     */
    public function testFilterWithNoHtml()
    {
        $str = 'The string to parse';

        $this->assertEquals($str, $this->filter->filter($str));
    }
}
