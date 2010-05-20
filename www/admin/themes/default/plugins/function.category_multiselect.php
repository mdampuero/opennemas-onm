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
 * Smarty function plugin: category_multiselect
 * 
 * <code>
 * {category_multiselect selected=$array_of_pk_categories id="form-field-name" name="categories[]"}
 * </code>
 */
function smarty_function_category_multiselect($params, &$smarty)
{
    if(!isset($params['id'])) {
        $smarty->_trigger_fatal_error('[plugin] category_multiselect needs a "id" param');
        return;
    }
    
    // Plugin parameters
    $id       = $params['id'];
    $name     = (isset($params['name']))? $params['name']: $id .'[]';
    $selected = (isset($params['selected']))? $params['selected']: array();
    
    
    $catMgr = CategoryManager::getInstance();
    $categories = $catMgr->getCategories();
    
    $jsCode = '<script type="text/javascript">$(function(){ $("#' . $id . '").multiselect(); });</script>';
    
    // TODO: evaluate to implement $params['size'] ?
    $html = '<select class="multiselect" name="' . $name . '" id="' . $id . '" multiple="multiple" style="width: 380px; height: 250px;">';
    
    foreach($categories as $cat) {
        $html .= '<option value="' . $cat->pk_category . '"';
        
        if(in_array($cat->pk_category, $selected)) {
            $html .= ' selected="selected"';
        }
        
        $html .= '>' . $cat->title . '</option>';
    }
    
    $html .= '</select>';

    return $html . $jsCode;
}