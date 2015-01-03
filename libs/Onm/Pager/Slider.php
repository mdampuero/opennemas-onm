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
     * @param int $options number of elements
     *
     * @return Pager the pager object
     **/
    public static function create($options)
    {
        // Check required options and set default ones
        if (!array_key_exists('base_url', $options)) {
            throw new \LogicException('Provide a base_url for the paginator component.');
        }

        if (!array_key_exists('elements_per_page', $options)) {
            $options['elements_per_page'] = 10;
        }

        $pageComponent = (strpos($options['base_url'], '?')) ? '&page=%d' : '?page=%d';

        $defaultOptions = [
            'mode'        => 'Sliding',
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'perPage'     => $options['elements_per_page'],
            'totalItems'  => $options['total_items'],
            'fileName'    => $options['base_url'].$pageComponent,
        ];

        unset($options['base_url']);
        unset($options['elements_per_page']);

        // Merge default options with providers
        $options = array_merge($defaultOptions, $options);

        // Build the pager
        return \Pager::factory($options);
    }
}
