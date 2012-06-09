<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require_once __DIR__.'/../app/autoload.php';

$configFile = implode(DIRECTORY_SEPARATOR, array(
    APPLICATION_PATH, 'config', 'config.inc.php'
));

if (!isset($request)) {
    $request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
}

if (file_exists($configFile)) {

    require $configFile;
    require SITE_LIBS_PATH.'/functions.php';
    require_once 'Application.php';
    \Application::initAutoloader();

    // Loads one ONM instance from database
    $im = \Onm\Instance\InstanceManager::getInstance();
    try {
        $instance = $im->load($_SERVER['SERVER_NAME']);
    } catch (\Onm\Instance\NotActivatedException $e) {
        echo 'Instance not activated';
        die();
    } catch (\Onm\Instance\NotFoundException $e) {
        echo 'Instance not found';
        die();
    }

    $app = \Application::load();

} else {
    $errorPage =
        file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'500.html');
    echo $errorPage;
    die();
}