<?php
/*
 * -------------------------------------------------------------
 * File:     	modifier.smarty_convert_to_slug.php
 * convert the value into a slug-slug
 *
 */
function smarty_modifier_convert_to_slug($value)
{
     $output = \Onm\StringUtils::getTitle($value);

    return $output;
}
