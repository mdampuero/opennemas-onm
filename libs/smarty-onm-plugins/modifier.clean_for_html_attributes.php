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
function smarty_modifier_clean_for_html_attributes($string)
{
    
    $patterns = array('/"/', '/\'/');
    $replace = array('', '');
       
    return preg_replace($patterns,$replace, $string);
 
}


