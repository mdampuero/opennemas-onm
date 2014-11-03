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
 * Name:     mb_upper<br>
 * Purpose:  convert string to uppercase
 * @param string
 * @return string
 */
function smarty_modifier_mb_upper($string)
{
    return mb_strtoupper($string, 'UTF-8');
}


