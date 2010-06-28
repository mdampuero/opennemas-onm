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
 * Pane with categories form
 * 
 * <code>
 * {pane_categories value=$widget legend="Legend title"}
 * {pane_categories selected=$scategories_selected}
 * </code>
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_pane_categories($params, &$smarty)
{    
    $selected = array();
    
    if( isset($params['value']) ) {
        // Get categories of content
        
        /**
         * @deprecated Use getTemplateVars
         */
        $request = $smarty->get_template_vars('request');                        
        
        if($request->getActionName() == "update") {
            $selected =  $params['value']->belongsToCategories();
        }
        
    } elseif(isset($params['selected'])) {
        // Categories from selected param
        $selected = $params['selected'];
    }    
    
    // Don't use $smarty to prevent assign values to "$content" variable
    $tpl = new TemplateAdmin(TEMPLATE_ADMIN);    
    $tpl->assign('selected', $selected);
    
    if(isset($params['legend'])) {
        $tpl->assign('legend', $params['legend']);
    }
    
    return $tpl->fetch('panes/categories.tpl');
}