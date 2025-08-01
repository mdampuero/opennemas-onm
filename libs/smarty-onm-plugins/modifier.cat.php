<?php
/**
 * Concatenate a value to a variable
 *
 * @param string
 * @param string
 *
 * @return string
 */
function smarty_modifier_cat($string, $cat)
{
    return $string . $cat;
}
