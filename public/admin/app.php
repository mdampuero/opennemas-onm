<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

require '../../app/autoload.php';

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
$generator = new \Symfony\Component\Routing\Generator\UrlGenerator($routes, $context);

// Dispatch the response
$dispatcher = new \Onm\Dispatcher($matcher, $request);
$dispatcher->dispatch();