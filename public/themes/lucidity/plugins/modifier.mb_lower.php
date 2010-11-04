<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty lower modifier plugin
 *
 * Type:     modifier<br>
 * Name:     mb_lower<br>
 * Purpose:  convert string to lowercase
 * @param string
 * @return string
 */
function smarty_modifier_mb_lower($string)
{
    return mb_strtolower($string, 'UTF-8');
}


