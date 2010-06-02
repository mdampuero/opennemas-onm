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
 * Pane with themes form
 * 
 * <code>
 * {pane_seo value=$page legend="Legend title"}
 * </code>
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_pane_themes($params, &$smarty)
{
    // Don't use $smarty to prevent assign values to "$item" variable
    $tpl = new TemplateAdmin(TEMPLATE_ADMIN);
    
    $themeMgr = ThemeManager::getInstance();
    $tpl->assign('themes', $themeMgr->getThemes());
    
    $tpl->assign('grids', GridManager::getAllGrids());
    
    foreach($params as $k => $v) {
        $tpl->assign($k, $v);
    }
    
    return $tpl->fetch('panes/themes.tpl');
}
