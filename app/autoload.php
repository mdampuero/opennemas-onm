<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Paths settings
define('SITE_PATH',        realpath(APPLICATION_PATH. DIRECTORY_SEPARATOR . "public" ).DIRECTORY_SEPARATOR);
define('SITE_LIBS_PATH',   realpath(SITE_PATH . "libs") . DIRECTORY_SEPARATOR);
define('SITE_CORE_PATH',   realpath(SITE_PATH.DIRECTORY_SEPARATOR."core").DIRECTORY_SEPARATOR);
define('SITE_VENDOR_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."vendor").DIRECTORY_SEPARATOR);
define('SITE_MODELS_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."app/models").DIRECTORY_SEPARATOR);
define('SITE_WS_API_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."app/rest").DIRECTORY_SEPARATOR);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    SITE_CORE_PATH, SITE_LIBS_PATH, SITE_VENDOR_PATH, SITE_MODELS_PATH, SITE_WS_API_PATH, get_include_path(),
)));

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
$loader->registerNamespaceFallback(SITE_WS_API_PATH);
$loader->useIncludePath(true);

$loader->register();