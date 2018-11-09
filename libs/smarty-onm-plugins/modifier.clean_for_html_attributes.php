<?php
/**
 * Smarty lower modifier plugin
 *
 * Type:     modifier
 * Name:     clean_for_html_attribute
 *
 * @param string $html
 *
 * @return string
 */
function smarty_modifier_clean_for_html_attributes($html)
{
    return preg_replace(['/"/', '/\'/'], ['', ''], $html);
}
