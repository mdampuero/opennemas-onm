<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../tmp/bootstrap.php.cache';

// Little hack to allow final slashes in the url
$_SERVER['REQUEST_URI'] = \Onm\StringUtils::normalizeUrl($_SERVER['REQUEST_URI']);

if (file_exists(APPLICATION_PATH.'/.development')) {
    $kernel = new AppKernel('dev', true);
    Debug::enable();
} else {
    $kernel = new AppKernel('prod', false);
}
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
