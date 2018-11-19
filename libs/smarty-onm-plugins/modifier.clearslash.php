<?php
/**
 * Clear slashs in a string provided
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_clearslash($string)
{
    $clearSlash = function ($text) {
        $text = stripslashes($text);
        $text = str_replace("\\", '', $text);
        return stripslashes($text);
    };

    if (is_array($string)) {
        return array_map($clearSlash, $string);
    }

    return $clearSlash($string);
}
