<?php
/**
 * Normalize string
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_normalize($string)
{
    return \Onm\StringUtils::normalize($string);
}
