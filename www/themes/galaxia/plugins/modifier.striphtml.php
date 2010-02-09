<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty striphtml plugin
 * @author   Tomás Vilariño <vifito at gmail dot com>
 * @param string
 * @return string
 */
function smarty_modifier_striphtml($string, $tags_allowed='')
{
    return strip_tags($string, $tags_allowed);
}