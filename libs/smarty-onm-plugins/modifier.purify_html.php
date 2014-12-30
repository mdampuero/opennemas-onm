<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNemas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNemas
 * @package    OpenNemas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Modifier smarty plugin
 *
 * {$extra_long_html|truncate:"30"|purify_html}
 * Fix truncated html content
 */
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
