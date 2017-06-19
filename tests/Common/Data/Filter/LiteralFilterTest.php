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

use Common\Data\Filter\LiteralFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for LiteralFilter class.
 */
class LiteralFilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'hasParameter' ])
            ->getMock();
    }

    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $filter = new LiteralFilter($this->container, [ 'value' => 'grault' ]);

        $this->assertEquals('grault', $filter->filter(null));
        $this->assertEquals('grault', $filter->filter(''));
        $this->assertEquals('grault', $filter->filter('fred'));
    }

    /**
     * Tests filter.
     */
    public function testFilterWithNoValue()
    {
        $filter = new LiteralFilter($this->container, [ ]);

        $this->assertEquals('', $filter->filter(null));
        $this->assertEquals('', $filter->filter(''));
        $this->assertEquals('', $filter->filter('fred'));
    }
}
