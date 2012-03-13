<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require_once __DIR__.'/../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

// Initialize the autoloader (use apc if available)
use Symfony\Component\ClassLoader\UniversalClassLoader;
if (extension_loaded('apc')) {
    require_once __DIR__.'/../vendor/Symfony/Component/ClassLoader/ApcUniversalClassLoader.php';
    $loader = new Symfony\Component\ClassLoader\ApcUniversalClassLoader('onm.framework.autoloader.');
} else {
    $loader = new UniversalClassLoader();
}

// Registering namespaces
$loader->registerNamespaces(array(
    'Onm'              => __DIR__.'/../vendor',
    'Symfony'          => __DIR__.'/../vendor',
    'Panorama'          => __DIR__.'/../vendor/Panorama',
));

// Ŕegistering prefixes
$loader->registerPrefix("Zend_", __DIR__.'/../vendor/Zend/');

// Registering fallbacks and include path usage
$loader->registerNamespaceFallback(__DIR__.'/core/');
$loader->registerNamespaceFallback(SITE_MODELS_PATH);
$loader->useIncludePath(true);

$loader->register();