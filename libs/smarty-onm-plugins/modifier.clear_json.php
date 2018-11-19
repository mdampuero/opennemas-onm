<?php
/**
 * Smarty clean json strings modifier plugin
 *
 * @return string
 */
function smarty_modifier_clear_json($content)
{
    return str_replace(['\'', '"', '&#39;' ], ['\\\'', '\'', '\\\'' ], $content);
}
