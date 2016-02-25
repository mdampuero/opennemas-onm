<?php

namespace Tests\Common\Migration;

use Common\Migration\Filter\HtmlFilter;

class HtmlFilterTest extends \PHPUnit_Framework_TestCase
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
