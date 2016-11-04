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

use Common\Core\Component\Filter\SlugFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for SlugFilter class.
 */
class SlugFilterTest extends KernelTestCase
{
    public function testFilterWithNoParameters()
    {
        $str = 'The string to convert';

        $filter = new SlugFilter();
        $filter->utils = \Mockery::mock('\Onm\StringUtils_' . uniqid());
        $filter->utils->shouldReceive('getTitle')->once()
            ->with($str, false, '.');

        $filter->filter($str);
    }

    public function testFilterWithParameters()
    {
        $str    = 'The string to convert';
        $params = [ 'separator' => '-', 'stopList' => false ];

        $filter = new SlugFilter($params);
        $filter->utils = \Mockery::mock('\Onm\StringUtils_' . uniqid());
        $filter->utils->shouldReceive('getTitle')->once()
            ->with($str, $params['stopList'], $params['separator']);

        $filter->filter($str, $params);
    }
}
