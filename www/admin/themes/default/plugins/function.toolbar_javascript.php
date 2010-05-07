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
 

function smarty_function_toolbar_javascript($params, &$smarty)
{
    if(!isset($params['toolbar'])) {
        $smarty->_trigger_fatal_error('[plugin] toolbar_javascript needs a "toolbar" param');
        return;
    }
    
    if(!isset($params['name'])) {
        $smarty->_trigger_fatal_error('[plugin] toolbar_javascript needs a "name" param');
        return;
    }
    
    if(!isset($params['text'])) {
        $smarty->_trigger_fatal_error('[plugin] toolbar_javascript needs a "text" param');
        return;
    }
    
    if(isset($params['query'])) {       
        $params['query'] = json_decode('{' . $params['query'] . '}', true);
    }  
    
    $toolbar = Onm_View_Helper_Toolbar::getInstance($params['toolbar']);    
    unset($params['toolbar']);
    
    $name = $params['name'];
    unset($params['name']);
    
    $text = $params['text'];
    unset($params['text']);
    
    $events = array();
    foreach(Onm_View_Helper_Toolbar_Javascript::$allowEvents as $evt) {
        if(isset($params[$evt])) {
            $events[$evt] = $params[$evt];
            unset($params[$evt]);
        }
    }
    $params['events'] = $events;
    
    $button = new Onm_View_Helper_Toolbar_Javascript($name, $text, $params);
    $toolbar->appendButton($button);
}
