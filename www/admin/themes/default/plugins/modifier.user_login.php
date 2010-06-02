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
 * Smarty user_login modifier plugin
 *
 * Type:     modifier<br>
 * Name:     user_login<br>
 * Purpose:  Return login of user 
 * @author   Tomás Vilariño <vifito at gmail dot com>
 * @param int
 * @return string
 */
function smarty_modifier_user_login($pk_user)
{
    $userMgr = UserManager::getInstance();    
    $user = $userMgr->getUserById($pk_user);
    
    if($user == null) {
        return '';
    }
    
    return $user->login;
}