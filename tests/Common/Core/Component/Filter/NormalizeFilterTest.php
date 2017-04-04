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

use Common\Core\Component\Filter\NormalizeFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for NormalizeFilter class.
 */
class NormalizeFilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $this->filter = new NormalizeFilter($container);
    }

    /**
     * Tests filter
     */
    public function testFilter()
    {
        $this->assertEquals('bar', $this->filter->filter('Bar'));
        $this->assertEquals('foo-mumble', $this->filter->filter('foo mumble'));
        $this->assertEquals('espana', $this->filter->filter('::: España :::'));
    }
}
