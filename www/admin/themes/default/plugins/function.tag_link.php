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
 * 
 * SEE: block.stylesection.php
 * <code>
 * {tag_link href="jquery.css" section="head" type="text/css" rel="stylesheet"}
 * </code>
 */
function smarty_function_tag_link($params, &$tpl)
{
    if(!isset($params['href'])) {
        $smarty->_trigger_fatal_error('[plugin] link_css needs a "href" param');
        return;
    }
    
    $href = $params['href'];
    unset($href);
    
    $section = (!isset($params['section']))? 'head': $params['section'];
    unset($params['section']);
    
    $tpl->addStyle($href, $section, $params);
}