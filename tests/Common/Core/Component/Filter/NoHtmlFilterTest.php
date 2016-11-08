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
    public function setUp()
    {
        $this->filter = new NoHtmlFilter();
    }

    public function testFilterWithHtmlEntities()
    {
        $str = '<p>The string to parse</p>';

        $this->assertEquals('The string to parse', $this->filter->filter($str));
    }

    public function testFilterWithNoHtmlEntities()
    {
        $str = 'The string to parse';

        $this->assertEquals($str, $this->filter->filter($str));
    }
}
