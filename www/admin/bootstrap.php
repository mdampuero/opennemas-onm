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

/* *****************************************************************************
 ______      ___      _____     _  __    _____     _  _    _____    
|      \\   / _ \\   / ____||  | |/ //  |  ___||  | \| || |  __ \\  
|  --  //  / //\ \\ / //---`'  | ' //   | ||__    |  ' || | |  \ || 
|  --  \\ |  ___  ||\ \\___    | . \\   | ||__    | .  || | |__/ || 
|______// |_||  |_|| \_____||  |_|\_\\  |_____||  |_|\_|| |_____//  
`------`  `-`   `-`   `----`   `-` --`  `-----`   `-` -`   -----`
****************************************************************************** */

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once '../configs/config.inc.php';

/* *************************************************************************** */
// Set include_path {{{
$separador	= PATH_SEPARATOR;
function __filtro_libs($var){ return $var!=''; }
$dirs = array_filter(explode($separador, ini_get('include_path')), "__filtro_libs");
$dirs[count($dirs)] = SITE_PATH . "libs/";
ini_set('include_path', implode($dirs, $separador));
// }}}


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
if( APPLICATION_ENV == 'development' ) {
    $writer = new Zend_Log_Writer_Firebug();
} else {
    $writer = new Zend_Log_Writer_Stream(SYS_LOG);
}

$logger = new Zend_Log($writer);
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

// No render by default
$front->setParam('noViewRenderer', true);
$front->setControllerDirectory('./controllers');

// Dispatch
$front->dispatch();