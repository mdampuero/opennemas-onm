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

use Common\Core\Component\Filter\FilterManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for FilterManager class.
 */
class FilterManagerTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')
            ->getMock();

        $this->fm = new FilterManager($container);
    }

    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $this->fm->filter('youtube_id', 'frog');
    }

    /**
     * Tests filter when the filter to apply is invalid.
     *
     * @expectedException Common\Core\Component\Exception\Filter\InvalidFilterException
     */
    public function testFilterWhenInvalidFilter()
    {
        $this->fm->filter('gorp', 'frog');
    }
}
