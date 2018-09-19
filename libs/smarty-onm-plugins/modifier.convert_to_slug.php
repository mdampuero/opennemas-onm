<?php
/**
 * Converts the value into a slug
 *
 * @param string $value
 *
 * @return string
 */
function smarty_modifier_convert_to_slug($value)
{
    return \Onm\StringUtils::getTitle($value);
}
