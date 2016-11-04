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

use Common\Core\Component\Filter\HtmlFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for HtmlFilter class.
 */
class HtmlFilterTest extends KernelTestCase
{
    public function setUp()
    {
        $this->filter = new HtmlFilter();
    }

    public function testFilterWithHtmlEntities()
    {
        $str      = '<p>The string</p><p>to</p><p>parse</p>';

        $expected = '&lt;p&gt;The string&lt;/p&gt;&lt;p&gt;to&lt;/p&gt;&lt;p&gt'
            . ';parse&lt;/p&gt;';

        $this->assertEquals($expected, $this->filter->filter($str));
    }

    public function testFilterWithNoHtmlEntities()
    {
        $str = 'The string to parse';

        $this->assertEquals($str, $this->filter->filter($str));
    }
}
