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

use Common\Core\Component\Filter\LiteralFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for LiteralFilter class.
 */
class LiteralFilterTest extends KernelTestCase
{
    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $filter = new LiteralFilter([ 'value' => 'grault' ]);

        $this->assertEquals('grault', $filter->filter(null));
        $this->assertEquals('grault', $filter->filter(''));
        $this->assertEquals('grault', $filter->filter('fred'));
    }

    /**
     * Tests filter.
     */
    public function testFilterWithNoValue()
    {
        $filter = new LiteralFilter([ ]);

        $this->assertEquals('', $filter->filter(null));
        $this->assertEquals('', $filter->filter(''));
        $this->assertEquals('', $filter->filter('fred'));
    }
}
