<?php
/**
 * Converts string to lowercase
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_mb_lower($string)
{
    return mb_strtolower($string, 'UTF-8');
}
