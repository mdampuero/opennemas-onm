<?php
/**
 * Filters a string for AMP pages.
 *
 * @param  string $string The string to clean.
 *
 * @return string The cleaned string.
 */
function smarty_modifier_amp($string)
{
    return getService('data.manager.filter')
        ->set($string)
        ->filter('amp')
        ->get();
}
