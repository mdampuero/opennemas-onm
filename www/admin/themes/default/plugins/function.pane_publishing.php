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
 * Pane with options to publish a content
 * 
 * <code>
 * {pane_publishing content=$widget legend="Legend title"}
 * {pane_publishing content=$widget}
 * </code>
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_pane_publishing($params, &$smarty)
{
    // Don't use $smarty to prevent assign values to "$content" variable
    $tpl = new TemplateAdmin(TEMPLATE_ADMIN);    

    if(isset($params['content'])) {
        $tpl->assign('content', $params['content']);
    }
    
    if(isset($params['legend'])) {
        $tpl->assign('legend', $params['legend']);
    }
    
    return $tpl->fetch('panes/publishing.tpl');
}