<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once APPLICATION_PATH . '/../configs/config.inc.php';

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

require_once 'PHPUnit/Framework/Error/Notice.php';
require_once 'PHPUnit/Framework/Error/Warning.php';

$autoloader->registerNamespace('Onm');
// }}}