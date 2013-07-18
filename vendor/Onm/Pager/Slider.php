<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Pager;

/**
* Wrapper for easing the usageof Pager class
*/
class Slider
{
    public static function create($totalElements, $itemsPerpage, $baseUrl)
    {
        $pageComponent = (strpos($baseUrl, '?')) ? '&page=%d' : '?page=%d';

        // Build the pager
        return \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerpage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $totalElements,
                'fileName'    => $baseUrl.$pageComponent,
            )
        );
    }
}
