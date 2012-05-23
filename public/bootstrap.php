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

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Routing\RouteCollection,
    Symfony\Component\Routing\Matcher\UrlMatcher,
    Symfony\Component\Routing\RequestContext,
    Symfony\Component\Routing\Route;

// Load the available route collection
$routes = new RouteCollection();
foreach (glob(APP_PATH.'/*/routes.php') as $routeFile) {
    require $routeFile;
}

// Create the request object
$request = Request::createFromGlobals();

// Create the Request context from the request, useful for the matcher
$context = new RequestContext();
$context->fromRequest($request);

// Inialize the url matcher
$matcher = new UrlMatcher($routes, $context);

//Initialize the url generator
global $generator;
$generator = new \Symfony\Component\Routing\Generator\UrlGenerator($routes, $context);

if (file_exists($configFile)) {

    require $configFile;
    require SITE_LIBS_PATH.'/functions.php';
    require_once 'Application.php';
    \Application::initAutoloader();

    // Loads one ONM instance from database
    $im = new \Onm\Instance\InstanceManager($onmInstancesConnection);
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
    $errorPage =  file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'500.html');
    echo $errorPage;
    die();
}