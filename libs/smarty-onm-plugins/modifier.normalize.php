<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty normalize modifier plugin
 *
 * Type:     modifier<br>
 * Name:     normalize<br>
 * Purpose:  normalize string
 * @param string
 * @return string
 */
function smarty_modifier_normalize($string)
{
    return \Onm\StringUtils::normalize($string);
}
