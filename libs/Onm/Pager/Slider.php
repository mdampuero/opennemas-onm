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
* Wrapper for easing the usage of Pager class
*/
class Slider
{
    /**
     * Creates an slider pager from a set of parameters
     *
     * @param int $totalElements number of elements
     * @param int $itemsPerpage number of elements to show per page
     * @param string $baseUrl the base url to use in the pager
     *
     * @return Pager the pager object
     **/
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
