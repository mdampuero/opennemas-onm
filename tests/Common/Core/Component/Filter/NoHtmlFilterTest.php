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

use Common\Core\Component\Filter\NoHtmlFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for HtmlFilter class.
 */
class NoHtmlFilterTest extends KernelTestCase
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
     * Tests filter when the string contains HTML.
     */
    public function testFilterWithHtmlEntities()
    {
        $str = '<p>The string to parse</p>';

        $this->assertEquals('The string to parse', $this->filter->filter($str));
    }

    /**
     * Tests filter when the string does not contain HTML.
     */
    public function testFilterWithNoHtmlEntities()
    {
        $str = 'The string to parse';

        $this->assertEquals($str, $this->filter->filter($str));
    }
}
