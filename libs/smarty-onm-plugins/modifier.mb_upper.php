<?php
/**
 * Converts string to uppercase
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_mb_upper($string)
{
    return mb_strtoupper($string, 'UTF-8');
}
