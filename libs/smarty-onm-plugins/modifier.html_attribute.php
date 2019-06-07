<?php
/**
 * Smarty lower modifier plugin
 *
 * @param string $html
 *
 * @return string
 */
function smarty_modifier_html_attribute($html)
{
    return \Onm\StringUtils::htmlAttribute($html);
}
