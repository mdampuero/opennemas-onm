<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
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
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Onm_Filter_HtmlPurge
 * 
 * @package    Onm
 * @subpackage Filter
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Slug.php 1 2010-01-14 13:22:43Z vifito $
 */
class Onm_Filter_HtmlPurge implements Zend_Filter_Interface
{
    /**
     * Convert value to a balanced html
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        // Clean unterminated tag starting with character "<"
        $output = preg_replace('/<[^>]*$/', '', $value);
        
        // Load HTML
        $dom = @DOMDocument::loadHTML('<html><body>' . $output . '</body></html>');
        
        // Save to XML
        $output = $dom->saveXML();
        
        // Remove xml prolog and DOCTYPE
        $output = str_replace('<?xml version="1.0" standalone="yes"?>'."\n", '', $output);
        $output = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'."\n", '', $output);
        
        // Get HTML content
        $output = preg_replace('@^<html><body>(.*?)</body></html>$@', '\1', $output);
        
        return $output;
    }        
}