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
    public function setUp()
    {
        $this->filter = new ReplaceFilter();
    }

    public function testFilter()
    {
        $this->assertEquals(
            'foo waldo mumble',
            $this->filter->filter(
                'foo norf mumble',
                [ 'pattern' => 'norf', 'replacement' => 'waldo' ]
            )
        );

        $this->assertEquals(
            '/path/to/wobble',
            $this->filter->filter(
                'garply://wobble',
                [ 'pattern' => 'garply://', 'replacement' => '/path/to/' ]
            )
        );

    }
}
