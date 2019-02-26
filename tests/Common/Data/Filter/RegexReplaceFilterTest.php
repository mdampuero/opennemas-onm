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

use Common\Data\Filter\RegexReplaceFilter;

/**
 * Defines test cases for ReplaceFilter class.
 */
class RegexReplaceFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $container = $this->getMockBuilder('Container')
            ->setMethods([ 'hasParameter' ])
            ->getMock();

        $filter = new RegexReplaceFilter(
            $container,
            [ 'pattern' => '@norf@', 'replacement' => 'waldo' ]
        );

        $this->assertEquals('foo waldo mumble', $filter->filter('foo norf mumble'));

        $filter = new RegexReplaceFilter(
            $container,
            [ 'pattern' => '@garply://@', 'replacement' => '/path/to/' ]
        );

        $this->assertEquals('/path/to/wobble', $filter->filter('garply://wobble'));

        $filter = new RegexReplaceFilter($container, [ 'replacement' => 'wobble' ]);

        $this->assertEquals('mumble', $filter->filter('mumble'));
    }
}
