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

use Common\Core\Component\Filter\ReplaceFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for ReplaceFilter class.
 */
class ReplaceFilterTest extends KernelTestCase
{
    public function testFilter()
    {
        $filter = new ReplaceFilter(
            [ 'pattern' => 'norf', 'replacement' => 'waldo' ]
        );

        $this->assertEquals('foo waldo mumble', $filter->filter('foo norf mumble'));

        $filter = new ReplaceFilter(
            [ 'pattern' => 'garply://', 'replacement' => '/path/to/' ]
        );

        $this->assertEquals('/path/to/wobble', $filter->filter('garply://wobble'));

        $filter = new ReplaceFilter([ 'replacement' => 'wobble' ]);

        $this->assertEquals('mumble', $filter->filter('mumble'));
    }
}
