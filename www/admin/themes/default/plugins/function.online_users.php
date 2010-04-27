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
 
function smarty_function_online_users($params, &$smarty=null)
{
    // Sessions
    $session  = Zend_Registry::get('session');
    $sessions = $session->getSessions();    
    $smarty->assign('num_sessions', count($sessions));
    
    // Check gmail inbox
    $mailbox = null;
    if(isset($_SESSION['authGmail'])) {
        $user = new User();
        $mailbox = $user->cache->parseGmailInbox(base64_decode($_SESSION['authGmail']));        
    }
    $smarty->assign('mailbox', $mailbox);
    
    return $smarty->fetch('online_users.tpl');
}