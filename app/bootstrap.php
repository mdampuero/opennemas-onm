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

$configFile = implode(
    DIRECTORY_SEPARATOR,
    array(APPLICATION_PATH, 'config', 'config.inc.php')
);

mb_internal_encoding("UTF-8");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;

// We have to use this for ws and mobile
if (!isset($routes)) {
    $routes = new RouteCollection();
}

// Create the request object
$request = Request::createFromGlobals();
$request->setTrustedProxies(array('127.0.0.1'));

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
    require_once 'Application.php';

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
    $errorPage =
        file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'500.html');
    echo $errorPage;
    die();
}
