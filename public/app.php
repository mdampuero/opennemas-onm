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

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Little hack to allow final slashes in the url
$_SERVER['REQUEST_URI'] = normalizeUrl($_SERVER['REQUEST_URI']);

$configFile = implode(
    DIRECTORY_SEPARATOR,
    array(APPLICATION_PATH, 'config', 'config.inc.php')
);
require_once $configFile;


$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
// var_dump($kernel->getContainer()->get('router')->getRouteCollection());die();

$response->send();
$kernel->terminate($request, $response);

die();

Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$request->setTrustedProxies(array('127.0.0.1'));

$sc = include __DIR__.'/../app/container.php';

$framework = $sc->get('framework');
$response = $framework->handle($request);
$response->send();
$framework->terminate($request, $response);
