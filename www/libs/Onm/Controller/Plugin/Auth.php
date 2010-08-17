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


class Onm_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		$uri = $request->getRequestUri();
		$uri = strtolower($uri);
        
        $prefix = '/admin';
		if (strncmp($uri, $prefix, strlen($prefix)) != 0) {
			return;
		}				
        
        // FIXME: convert webdav controller in a standalone service
        $controllerName = $request->getControllerName();
        
        $session = new Zend_Session_Namespace();
		if( !isset($session->userid) && ($controllerName != 'webdav') )
        {
			$request->setControllerName('user')
					->setActionName('login')
					->setDispatched(true);
		}
	}
}