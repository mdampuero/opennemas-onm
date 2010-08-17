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
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Bootstrap
 * 
 * @package    Zend
 * @subpackage Application
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Bootstrap.php 1 2010-01-15 13:29:15Z vifito $
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    protected function _initView()
    {
        $tpl = new TemplateAdmin(TEMPLATE_ADMIN);
        if( APPLICATION_ENV == 'development' ) {
            $tpl->force_compile = true;
        }        
        Zend_Registry::set('tpl', $tpl);
        unset($tpl);
        
        // viewRenderer action helper
        $view = new Onm_View();
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
        $viewRenderer->setViewSuffix('tpl');
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    } 

    
    protected function _initPlugins()
    {
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');
        
        // Onm core plugins {{{
        $front->registerPlugin(new Onm_Controller_Plugin_Auth());
        $front->registerPlugin(new Onm_Controller_Plugin_Locale());
        $front->registerPlugin(new Onm_Controller_Plugin_Template());
        // }}}
    }
    
    
    protected function _initLogger()
    {
        $logger = new Zend_Log();
        
        // TODO: implement priorities
        $writer = new Zend_Log_Writer_Stream(SYS_LOG);
        $logger->addWriter($writer);
        
        if( APPLICATION_ENV == 'development' ) {
            $writer = new Zend_Log_Writer_Firebug();
            $logger->addWriter($writer);
        }
        
        Zend_Registry::set('logger', $logger);
        unset($writer);
        unset($logger);
    }
    
    
    protected function _initSession()
    {
        //Zend_Session::setOptions( array('strict'=>false) );
        $session = SessionManager::getInstance(OPENNEMAS_BACKEND_SESSIONS);
        $session->bootstrap();
        
        Zend_Registry::set('session', $session);
        unset($session);
    }
    
    
    protected function _initADO()
    {        
        $conn = ADONewConnection(BD_TYPE);
        $conn->Connect(BD_HOST, BD_USER, BD_PASS, BD_INST);
        
        Zend_Registry::set('conn', $conn);
        unset($conn);
    }
    
    
    protected function _initRoutes()
    {
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');
        
        $front->setParam('isBackend', true);
        
        // Routes
        $router = $front->getRouter();
        $router->removeDefaultRoutes();
        $routesPath = realpath(APPLICATION_PATH . '/../configs/routes-backend.xml');
        $router->addConfig( new Zend_Config_Xml($routesPath, APPLICATION_ENV) );
        
        // Frontend routes
        $routerFrontend = new Zend_Controller_Router_Rewrite();
        $routesPath = realpath(APPLICATION_PATH . '/../configs/routes-frontend.xml');
        $routerFrontend->addConfig(new Zend_Config_Xml($routesPath, APPLICATION_ENV));
        Zend_Registry::set('routerFrontend', $routerFrontend);
    }
    
    protected function _initZFDebug()
    {
        $zfdebugEntry = $this->getOption('zfdebugbar');
        
        if(!empty($zfdebugEntry) && isset($zfdebugEntry['enable']) &&
                ($zfdebugEntry['enable'] === '1')) {
            $options = array(
                'plugins' => array(
                    'Variables',
                    'Smarty',
                    'Adodb',
                    'Registry',
                    'File' => array('base_path' => APPLICATION_PATH),
                    'Memory', 
                    'Time', 
                    'Exception',
                    'Html',
                ),
                'jquery_path' => '/admin/themes/default/js/jquery-1.4.2.min.js',
                'image_path'  => '/admin/images/debugbar',
            );
            
            $zfdebug = new ZFDebug_Controller_Plugin_Debug($options);
            
            $this->bootstrap('frontController');
            $front = $this->getResource('frontController');
            $front->registerPlugin($zfdebug);
        }
    }
    
}

