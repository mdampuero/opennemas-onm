<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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

if (PHP_VERSION_ID < 70000) {
    include_once __DIR__ . '/../tmp/bootstrap.php.cache';
}

if (file_exists(APPLICATION_PATH . '/.development')
    || (array_key_exists('OPENNEMAS_ENV', $_ENV) && $_ENV["OPENNEMAS_ENV"] == 'dev')
) {
    Debug::enable();
    $kernel = new AppKernel('dev', true);
} else {
    $kernel = new AppKernel('prod', false);
}

if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}

$request = Request::createFromGlobals();

// As the LB ip's change, let's trust on all FORWARDED request headers that
// comes from them. See more: https://symfony.com/doc/2.8/deployment/proxies.html
Request::setTrustedProxies([
    '127.0.0.1',
    $request->server->get('REMOTE_ADDR')
], Request::HEADER_X_FORWARDED_ALL);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
