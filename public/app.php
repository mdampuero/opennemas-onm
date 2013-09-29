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
require_once __DIR__.'/../app/AppKernel.php';

// Load the available route collection
$routes = new \Symfony\Component\Routing\RouteCollection();

$routeFiles = glob(SRC_PATH.'/*/Resources/Routes/Routes.php');
foreach ($routeFiles as $routeFile) {
    require $routeFile;
}

// Little hack to allow final slashes in the url
$_SERVER['REQUEST_URI'] = normalizeUrl($_SERVER['REQUEST_URI']);

$configFile = implode(
    DIRECTORY_SEPARATOR,
    array(APPLICATION_PATH, 'config', 'config.inc.php')
);
require_once $configFile;

// Create the request object
$request = Request::createFromGlobals();
$request->setTrustedProxies(array('127.0.0.1'));

$framework = new Onm\Framework\Framework($routes);
$context = $framework->context;

//Initialize the url generator
global $generator;
$generator = $framework->generator;

$sc = include __DIR__.'/../app/container.php';

$framework->handle($request)->send();

// if (preg_match('@^/admin@', $request->getRequestUri(), $matches)) {
//     $sc->setParameter('dispatcher.exceptionhandler', 'Backend:Controllers:ErrorController:default');
// } elseif (preg_match('@^/manager@', $request->getRequestUri(), $matches)) {
//     $sc->setParameter('dispatcher.exceptionhandler', 'Manager:Controllers:ErrorController:default');
// } else {
//     $sc->setParameter('dispatcher.exceptionhandler', 'Frontend:Controllers:ErrorController:default');
// }
