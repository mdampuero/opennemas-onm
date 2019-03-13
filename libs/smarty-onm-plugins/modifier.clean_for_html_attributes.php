<?php
/**
 * Smarty lower modifier plugin
 *
 * @param string $html
 *
 * @return string
 */
function smarty_modifier_clean_for_html_attributes($html)
{
    return \Onm\StringUtils::htmlAttribute($html);
}
