<?php

/**
 * Defining application, libs, and other internal constants.
*/

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Paths settings
define('SITE_PATH',        realpath(APPLICATION_PATH. DIRECTORY_SEPARATOR . "public" ).DIRECTORY_SEPARATOR);
define('SITE_LIBS_PATH',   realpath(SITE_PATH . "libs") . DIRECTORY_SEPARATOR);
define('SITE_CORE_PATH',   realpath(SITE_PATH.DIRECTORY_SEPARATOR."core").DIRECTORY_SEPARATOR);
define('SITE_VENDOR_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."vendor").DIRECTORY_SEPARATOR);
define('SITE_MODELS_PATH', realpath(SITE_PATH.DIRECTORY_SEPARATOR."models").DIRECTORY_SEPARATOR);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    SITE_CORE_PATH, SITE_LIBS_PATH, SITE_VENDOR_PATH, SITE_MODELS_PATH, get_include_path(),
)));

$configFile = implode(DIRECTORY_SEPARATOR, array(
    APPLICATION_PATH, 'config', 'config.inc.php'
));

if (file_exists($configFile)) {
    
    require($configFile);
    require_once(SITE_CORE_PATH.'application.class.php');
    \Application::initAutoloader('*');
    
    // Loads one ONM instance from database
    $im = \Onm\Instance\InstanceManager::getInstance();
    $instance = $im->load($_SERVER['SERVER_NAME']);
    if (!$instance) {
        // This instance
        echo 'Instance not found';
        die();
    } else {
        if (
            $instance->activated != 1
            && !preg_match("@manager@", $_SERVER["PHP_SELF"])
        ) {
            echo 'Instance not activated. Pay for it, bitch!';
            die();
        }
    }
    $app = \Application::load();
    
} else {
    $errorPage =  file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'500.html');
    echo $errorPage;
    die();
}
