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

use Common\Core\Component\Filter\DateFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for DateFilter class.
 */
class DateFilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'hasParameter', 'getParameter' ])
            ->getMock();
    }

    /**
     * Tests filter when value is a timestamp string.
     */
    public function testFilterForTimestamp()
    {
        $str    = '1478220949';
        $filter = new DateFilter($this->container, [ 'timestamp' => true ]);

        $this->assertEquals('2016-11-04 00:55:49', $filter->filter($str));
    }

    /**
     * Tests filter when value is a human readable string.
     */
    public function testFilterForString()
    {
        $str    = 'Fri, 04 Nov 2016 00:55:49 GMT';
        $filter = new DateFilter($this->container);

        $this->assertEquals('2016-11-04 00:55:49', $filter->filter($str));
    }
}
