<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty capitalize modifier plugin
 *
 * Type:     modifier<br>
 * Name:     clearslash<br>
 * Purpose:  clear slashs
 * @author   Tomás Vilariño <vifito at gmail dot com>
 * @param string
 * @return string
 */
function smarty_modifier_clearslash($string)
{
    $clearSlash = function ($text) {
        $text = stripslashes($text);
        $text = str_replace("\\", '', $text);
        return stripslashes($text);
    };

    if (is_array($string)) {
        return array_map($clearSlash, $string);
    }

    return $clearSlash($string);
}
