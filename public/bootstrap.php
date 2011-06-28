<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../libs'),
    get_include_path(),
)));


$configFile = dirname(__FILE__).DIRECTORY_SEPARATOR
            .'..'.DIRECTORY_SEPARATOR.'config'
            .DIRECTORY_SEPARATOR. 'config.inc.php';

if (file_exists($configFile)) {
    require($configFile);
    require_once(SITE_CORE_PATH.'application.class.php');

    Application::importLibs('*');
    $app = Application::load();
} else {
    $errorPage =  file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'500.html');
    echo $errorPage;
    die();
}
