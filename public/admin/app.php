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

$routes = new RouteCollection();

foreach (glob(SITE_PATH.'/app/modules/*/routes.php') as $routeFile) {
    require $routeFile;
}

$routes->add('admin_system_settings', new Route(
    '/system/settings',
    array('_controller' => 'controllers/system_settings/system_settings.php')),
    '/admin'
);
$routes->add('admin_frontpage_list', new Route(
    '/frontpages',
    array('_controller' => 'controllers/frontpagemanager/frontpagemanager.php', 'action' => 'list')),
    '/admin'
);
$routes->add('admin_frontpage_list_with_category', new Route(
    '/frontpages/{category}',
    array('_controller' => 'controllers/frontpagemanager/frontpagemanager.php', 'action' => 'list')),
    '/admin'
);
$routes->add('admin_login', new Route(
    '/login',
    array('_controller' => 'login.php')),
    '/admin'
);
$routes->add('admin_welcome', new Route(
    '/',
    array('_controller' => 'index.php')),
    '/admin'
);
$context = new RequestContext();
$request = Request::createFromGlobals();

$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

$generator = new \Symfony\Component\Routing\Generator\UrlGenerator($routes, $context);
try {
    $parameters = $matcher->match(rtrim($request->getPathInfo(), '/'));
    foreach ($parameters as $param => $value) {
        $request->query->set($param, $value);
    }
    require $parameters['_controller'];
} catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
    require 'index.php';
}