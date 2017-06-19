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

use Common\Data\Filter\MetadataFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for MetadataFilter class.
 */
class MetadataFilterTest extends KernelTestCase
{
    public function setUp()
    {
        $this->filter = new MetadataFilter();

        $this->filter->utils = \Mockery::mock('\Onm\StringUtils_' . uniqid());
    }

    public function testFilter()
    {
        $str = 'The string to convert';

        $this->filter->utils->shouldReceive('getTags')->once()->with($str);

        $this->filter->filter($str);
    }
}
