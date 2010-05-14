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
 * <code>
 * {flashmessenger}
 * </code>
 */
function smarty_function_flashmessenger($params, &$smarty)
{
    $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    
    //get messages from previous requests
    $messages = $flashMessenger->getMessages();
    
    //add any messages from this request
    if ($flashMessenger->hasCurrentMessages()) {
        $messages = array_merge(
            $messages,
            $flashMessenger->getCurrentMessages()
        );
        //we don't need to display them twice.
        $flashMessenger->clearCurrentMessages();
    }
    
    if(count($messages) <= 0) {
        return '';
    }
    
    $template = '<li class="%s">%s</li>';
    $cssClass = 'flashmessenger';
    
    //initialise return string
    $output = '<ul class="' . $cssClass . '">';
    
    //process messages
    foreach ($messages as $message)
    {
        if (is_array($message)) {
            list($key,$message) = each($message);
        }
        $output .= sprintf($template, $key, $message);
    }
    
    $output .= "</ul>";
    
    return $output;
}