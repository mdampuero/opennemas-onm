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
 * Smarty plugin category_select
 * 
 * <code>
 * {category_select name="form_id" selected=$fk_category disableRecursive=$pk_category}
 *
 * <select name="manual-name">
 * {category_select}
 * </select>
 * </code>
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_category_select($params, &$smarty)
{
    $html = '';
    
    if(isset($params['name'])) {
        $html .= '<select name="' . $params['name'] . '" id="' . $params['name'] . '">';
    }
    
    $catMgr = CategoryManager::getInstance();    
    $tree = $catMgr->getTree();
    
    $options = array(        
        'tabChar'  => '&nbsp;&middot;&nbsp;',        
    );
    
    $disabled = array();
    
    if(isset($params['disableRecursive'])) {
        $options['disabled'] = array_merge(
            array($params['disableRecursive']),
            $catMgr->getDescendants($params['disableRecursive'])
        );
    }
    
    if(isset($params['selected'])) {
        $options['selected'] = $params['selected'];
    }
    
    $html .= $catMgr->getHtmlOptions($options, $tree);
    
    if(isset($params['name'])) {
        $html .= '</select>';
    }
    
    return $html;
}