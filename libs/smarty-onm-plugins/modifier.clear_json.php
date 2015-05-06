<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty clean json strings modifier plugin
 *
 * Type:     modifier<br>
 * Name:     clear_json<br>
 * Purpose:  clear json strings
 * @author   Alex <alex at openhost dot com>
 * @param string
 * @return string
 */
function smarty_modifier_clear_json($string)
{
    $search  = ['\'', '"', '&#39;' ];
    $replace = ['\\\'', '\'', '\\\'' ];

    $string = str_replace($search, $replace, $string);

    return $string;
}
