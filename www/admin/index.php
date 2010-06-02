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

/* ********************************************
 **   _                _                  _  **
 **  | |__   __ _  ___| | _____ _ __   __| | ** 
 **  | '_ \ / _` |/ __| |/ / _ \ '_ \ / _` | **
 **  | |_) | (_| | (__|   <  __/ | | | (_| | **
 **  |_.__/ \__,_|\___|_|\_\___|_| |_|\__,_| **
 **********************************************/

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once '../configs/config.inc.php';

/* *************************************************************************** */
// Autoload {{{
require_once 'Zend/Loader/Autoloader.php'; 
$autoloader = Zend_Loader_Autoloader::getInstance();

class OnmAutoloader
{
    public static function autoload($className)
    {
        $filename = strtolower($className);
        if( file_exists(SITE_PATH . 'core/' . $filename . '.class.php') ) {
            require SITE_PATH . 'core/' . $filename . '.class.php';            
        } else{            
            // Try convert MethodCacheManager to method_cache_manager
            $filename = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));
            
            if( file_exists( SITE_PATH . 'core/' . $filename . '.class.php') ) {
                require SITE_PATH . 'core/' . $filename . '.class.php';
            }
        }
    }
}
$autoloader->pushAutoloader(array('OnmAutoloader', 'autoload'));

// TODO: mejorar esto
require_once SITE_LIBS_PATH . '/adodb5/adodb.inc.php';
require_once SITE_LIBS_PATH . '/Pager/Pager.php';
require_once SITE_LIBS_PATH . '/smarty/Smarty.class.php';
require_once SITE_LIBS_PATH . '/template.class.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Onm');
// }}}

/* *************************************************************************** */
// Zend_Log config {{{
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
// }}}


/* *************************************************************************** */
// Zend_Db config and connect {{{
$params = array(
    'host'     => BD_HOST,
    'username' => BD_USER,
    'password' => BD_PASS,
    'dbname'   => BD_INST
);
$db = Zend_Db::factory('Pdo_Mysql', $params);
Zend_Db_Table::setDefaultAdapter($db);

Zend_Registry::set('db', $db);
unset($db); 
// }}}


/* *************************************************************************** */
// ADOConnection config {{{
$conn = &ADONewConnection(BD_TYPE);
$conn->Connect(BD_HOST, BD_USER, BD_PASS, BD_INST);            

// Log queries if environment equals than development
if( APPLICATION_ENV == 'development' ) {
    $conn->LogSQL();
}

Zend_Registry::set('conn', $conn);
unset($conn);
// }}}


/* *************************************************************************** */
// Session config {{{
Zend_Session::setOptions( array('strict'=>false) );
$session = SessionManager::getInstance(OPENNEMAS_BACKEND_SESSIONS);
$session->bootstrap();

Zend_Registry::set('session', $session);
unset($session);
// }}}


/* *************************************************************************** */
// Template {{{
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
Zend_Registry::set('tpl', $tpl);
unset($tpl);


/* *************************************************************************** */
// Frontcontroller instance and router initialization
$front = Zend_Controller_Front::getInstance();

// Routes
$router = $front->getRouter();
$router->removeDefaultRoutes();
$router->addConfig( new Zend_Config_Xml('../configs/routes-backend.xml', APPLICATION_ENV) );

// Load plugins
$front->registerPlugin( new Onm_Controller_Plugin_Auth()  );
$front->registerPlugin( new Onm_Controller_Plugin_Locale() );
$front->registerPlugin( new Onm_Controller_Plugin_Template() );

// FrontController params
$front->setParam('noViewRenderer', true) // No render by default
      ->setParam('isBackend', true);

$front->setControllerDirectory('./controllers');

// Dispatch
$front->dispatch();