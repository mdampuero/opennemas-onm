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

use Common\Data\Filter\MapifyFilter;

/**
 * Defines test cases for MapifyFilter class.
 */
class MapifyFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $container = $this->getMockBuilder('Container')
            ->setMethods([ 'hasParameter' ])
            ->getMock();

        $filter = new MapifyFilter($container, [ 'key' => 'norf' ]);

        $item = json_decode(json_encode([ 'norf' => 'mumble', 'qux' => 'bar' ]));

        $this->assertEquals([ 'mumble' => $item ], $filter->filter([ $item ]));
    }
}
