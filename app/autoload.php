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

// Paths settings
define('SITE_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."public").DIRECTORY_SEPARATOR);
define('SITE_LIBS_PATH', realpath(SITE_PATH . "libs") . DIRECTORY_SEPARATOR);
define('SITE_VENDOR_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."vendor").DIRECTORY_SEPARATOR);
define('SITE_CORE_PATH', realpath(SITE_VENDOR_PATH.DIRECTORY_SEPARATOR."core").DIRECTORY_SEPARATOR);
define('SITE_MODELS_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."app/models").DIRECTORY_SEPARATOR);
define('APP_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."app/").DIRECTORY_SEPARATOR);
define('SITE_WS_API_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."app/rest").DIRECTORY_SEPARATOR);

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            SITE_CORE_PATH,
            SITE_LIBS_PATH,
            SITE_VENDOR_PATH,
            SITE_MODELS_PATH,
            APP_PATH,
            SITE_WS_API_PATH,
            get_include_path(),
        )
    )
);
define('INSTALLATION_HASH', substr(hash('md5', APPLICATION_PATH), 0, 8));

require_once SITE_VENDOR_PATH.'functions.php';
require_once SITE_VENDOR_PATH.'/adodb5/adodb.inc.php';
require_once SITE_VENDOR_PATH.'/Pager/Pager.php';
require_once SITE_VENDOR_PATH.'/smarty/smarty-legacy/Smarty.class.php';
require_once SITE_VENDOR_PATH.'/Log.php';
require_once SITE_VENDOR_PATH.'/Template.php';

require_once __DIR__.'/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

// Initialize the autoloader (use apc if available)
use Symfony\Component\ClassLoader\UniversalClassLoader;

if (extension_loaded('apc')) {
    require_once __DIR__.'/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/ApcUniversalClassLoader.php';
    $loader = new Symfony\Component\ClassLoader\ApcUniversalClassLoader(INSTALLATION_HASH.'.');
} else {
    $loader = new UniversalClassLoader();
}

// Registering namespaces
$loader->registerNamespaces(
    array(
        'Onm'                                   => __DIR__.'/../vendor',
        'Symfony\Component\Routing'             => __DIR__.'/../vendor/symfony/routing',
        'Symfony\Component\HttpFoundation'      => __DIR__.'/../vendor/symfony/http-foundation',
        'Symfony\Component\ClasLoader'          => __DIR__.'/../vendor/symfony/class-loader',
        'Symfony\Component\DependencyInjection' => __DIR__.'/../vendor/symfony/dependency-injection',
        'Symfony\Component\Config'              => __DIR__.'/../vendor/symfony/config',
        'Symfony\Component\Yaml'                => __DIR__.'/../vendor/symfony/yaml',
        'Symfony\Component\Console'             => __DIR__.'/../vendor/symfony/console',
        'Symfony\Component\EventDispatcher'     => __DIR__.'/../vendor/symfony/event-dispatcher',
        'Panorama'                              => __DIR__.'/../vendor/frandieguez/panorama-php/lib',
        'Monolog'                               => __DIR__.'/../vendor/monolog/monolog/src',
    )
);

// SessionHandlerInterface
if (!interface_exists('SessionHandlerInterface')) {
    $loader->registerPrefixFallback(
        realpath(
            __DIR__.'/../vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/Resources/stubs'
        )
    );
}

// Ŕegistering prefixes
$loader->registerPrefixes(array("Zend_" => __DIR__.'/../vendor/Zend/'));

require (__DIR__.'/../vendor/Zend/Log.php');

// Registering fallbacks and include path usage
$loader->registerNamespaceFallback(SITE_MODELS_PATH);
$loader->registerNamespaceFallback(SITE_WS_API_PATH);
$loader->useIncludePath(true);

$loader->register();
