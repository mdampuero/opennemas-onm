<?php
/**
 * Modifier smarty plugin
 *
 * {$extra_long_html|truncate:"30"|purify_html}
 * Fix truncated html content
 *
 * @param string
 *
 * @return string
 */
// TODO: This file must be removed
function smarty_modifier_purify_html($value)
{
    // Clean unterminated tag starting with character "<"
    // $output = preg_replace('/<[^>]*$/', '', $value);

    // Load HTML
    // $dom = @DOMDocument::loadHTML('<html><body>' . $output . '</body></html>');

    // Save to XML
    // $output = $dom->saveXML();

    // Remove xml prolog and DOCTYPE
    /*$output = str_replace('<?xml version="1.0" standalone="yes"?>'."\n", '', $output);
    $output = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n", '', $output);

    // Get HTML content
    $output = preg_replace('@^<html><body>(.*?)</body></html>$@', '\1', $output);
    */

    return $value;
}
