<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

umask(0002);

// Force reset opcache if tmp/restart.txt file is present.
if (file_exists(__DIR__ . '/../tmp/restart.txt')) {
    @unlink(__DIR__ . '/../tmp/restart.txt');
    opcache_reset();
}

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = include __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../tmp/bootstrap.php.cache';

// Little hack to allow final slashes in the url
$_SERVER['REQUEST_URI'] = \Onm\StringUtils::normalizeUrl($_SERVER['REQUEST_URI']);

if (file_exists(APPLICATION_PATH . '/.development')
    || (array_key_exists('OPENNEMAS_ENV', $_ENV) && $_ENV["OPENNEMAS_ENV"] == 'dev')
) {
    $kernel = new AppKernel('dev', true);
    Debug::enable();
} else {
    $kernel = new AppKernel('prod', false);
}
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller
// instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
