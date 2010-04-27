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
 
function smarty_function_ypmenu($params, &$smarty=null)
{
    require_once( SITE_PATH . 'libs/menu.class.php');
    $menu = new Menu();    
    
    // FIXME: recuperar XML con una llamada a un método
    require SITE_ADMIN_PATH . 'include/menu.php';    
    $ypMenu = $menu->getMenu('YpMenu', $menuXml, 1);
    
    return $ypMenu;
}