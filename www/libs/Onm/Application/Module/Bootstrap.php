<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNemas project
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
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Onm_Application_Module_Bootstrap
 * 
 * @package    Onm
 * @subpackage Application
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Bootstrap.php 1 2010-08-16 11:43:09Z vifito $
 */
class Onm_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap
{
    
    protected function _initModule()
    {                
        $modulePath = realpath(
            APPLICATION_PATH . '/modules/' . strtolower($this->getModuleName())
        );        
        
        if ($modulePath !== false) {
            
            // If exists folder "views"
            if (file_exists($modulePath . '/views')) {
                
                if (file_exists($modulePath . '/views/tpls')) {
                    $tpl = Zend_Registry::get('tpl');
                    $tpl->addTemplateDir($modulePath . '/views/tpls');
                }                
                
                if (file_exists($modulePath . '/views/plugins')) {
                    $tpl->addPluginsDir($modulePath . '/views/plugins');
                }
            }
            
            // If exists routes.xml
            if (file_exists($modulePath . '/configs/routes.xml')) {
                $this->bootstrap('frontController');
                $front = $this->getResource('frontController');
                
                // Routes
                $router = $front->getRouter();
                $router->addConfig(
                    new Zend_Config_Xml(
                        $modulePath . '/configs/routes.xml', APPLICATION_ENV
                    )
                );
            }
        }
        
    }
    
    
    
}