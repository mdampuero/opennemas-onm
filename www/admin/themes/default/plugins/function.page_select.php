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
 * Smarty plugin page_select
 * 
 * <code>
 * {page_select name="form_id" selected=$fk_page disableRecursive=$pk_page}
 *
 * <select name="manual-name">
 * {page_select}
 * </select>
 * </code>
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_page_select($params, &$smarty)
{
    $html = '';
    
    if(isset($params['name'])) {
        $html .= '<select name="' . $params['name'] . '" id="' . $params['name'] . '">';
    }
    
    $pageMgr = PageManager::getInstance();    
    $tree = $pageMgr->getTree();
    
    $options = array(        
        'tabChar'  => '&nbsp;&middot;&nbsp;',        
    );
    
    $disabled = array();
    
    if(isset($params['disabled'])) {
        $options['disabled'] = array($params['disabled']);
    }
    
    if(isset($params['disableRecursive'])) {
        $options['disabled'] = array_merge(
            array($params['disableRecursive']),
            $pageMgr->getDescendants($params['disableRecursive'])
        );
    }
    
    if(isset($params['selected'])) {
        $options['selected'] = $params['selected'];
    }
    
    if(isset($params['selectedBySlug'])) {
        $options['selected'] = $pageMgr->getPageBySlug($params['selectedBySlug'])->pk_page;
    }
    
    // Check if it's the first node
    if(!$pageMgr->existsRoot()) {
        $html .= '<option value="0"> &oplus; </option>';
    } elseif(isset($params['selected'])) {
        if($params['selected'] == 0) {
            $html .= '<option value="0" selected="selected"> &oplus; </option>';
        }
    }
    
    $html .= $pageMgr->getHtmlOptions($options, $tree);
    
    if(isset($params['name'])) {
        $html .= '</select>';
    }
    
    return $html;
}