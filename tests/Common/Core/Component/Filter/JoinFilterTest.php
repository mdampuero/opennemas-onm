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

use Common\Core\Component\Filter\JoinFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for JoinFilter class.
 */
class JoinFilterTest extends KernelTestCase
{
    public function testFilterWithNoArray()
    {
        $filter = new JoinFilter();
        $str    = 'foo';

        $this->assertEquals($str, $filter->filter($str));
    }

    public function testFilterWithArrayAndDefaultGlue()
    {
        $filter = new JoinFilter();
        $str    = [ 'foo', 'bar' ];

        $this->assertEquals('foo,bar', $filter->filter($str));
    }

    public function testFilterWithArrayAndGlue()
    {
        $str    = [ 'foo', 'bar' ];
        $params = [ 'glue' => '-' ];

        $filter = new JoinFilter($params);

        $this->assertEquals('foo-bar', $filter->filter($str));
    }
}
