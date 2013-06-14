<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;

require __DIR__.'/../app/autoload.php';

// Load the available route collection
$routes = new \Symfony\Component\Routing\RouteCollection();

$routeFiles = glob(SRC_PATH.'/*/Resources/Routes/Routes.php');
foreach ($routeFiles as $routeFile) {
    require $routeFile;
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

$sc = include __DIR__.'/../app/container.php';

require 'bootstrap.php';

$timezone = \Onm\Settings::get('time_zone');
if (isset($timezone)) {
    $availableTimezones = \DateTimeZone::listIdentifiers();
    date_default_timezone_set($availableTimezones[$timezone]);
}

if (preg_match('@^/admin@', $request->getRequestUri(), $matches)) {
    $sc->setParameter('dispatcher.exceptionhandler', 'Backend:Controllers:ErrorController:default');
} elseif (preg_match('@^/manager@', $request->getRequestUri(), $matches)) {
    $sc->setParameter('dispatcher.exceptionhandler', 'Manager:Controllers:ErrorController:default');
} else {
    $sc->setParameter('dispatcher.exceptionhandler', 'Frontend:Controllers:ErrorController:default');
}

// Dispatch the response
$dispatcher = new \Onm\Framework\Dispatcher\Dispatcher($matcher, $request, $sc);
$dispatcher->dispatch();
