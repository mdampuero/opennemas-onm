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

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once '../configs/config.inc.php';

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../libs'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

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
$autoloader->registerNamespace('ZFDebug');
// }}}

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();


