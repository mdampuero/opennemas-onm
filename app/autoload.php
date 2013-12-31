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
define('SRC_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."src/").DIRECTORY_SEPARATOR);
define('SITE_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."public").DIRECTORY_SEPARATOR);
define('SITE_VENDOR_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."vendor").DIRECTORY_SEPARATOR);
define('SITE_CORE_PATH', realpath(SITE_VENDOR_PATH.DIRECTORY_SEPARATOR."core").DIRECTORY_SEPARATOR);
define('SITE_MODELS_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."app/models").DIRECTORY_SEPARATOR);
define('APP_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."app/").DIRECTORY_SEPARATOR);
define('SITE_WS_API_PATH', realpath(SRC_PATH.DIRECTORY_SEPARATOR."WebService/Handlers").DIRECTORY_SEPARATOR);
define('PP_CONFIG_PATH', APP_PATH.'/config/');

define('INSTALLATION_HASH', substr(hash('md5', APPLICATION_PATH), 0, 8));

require SITE_VENDOR_PATH.'/autoload.php';
if (file_exists(APPLICATION_PATH.'/.deploy.php')) {
    require APPLICATION_PATH.'/.deploy.php';
}

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();

// Registering namespaces
$loader->registerNamespaces(
    array(
        'Onm'               => __DIR__.'/../vendor',
        'Luracast\\Restler' => __DIR__.'/../vendor/luracast/restler/vendor/',
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

// Registering fallbacks and include path usage
$loader->registerNamespaceFallback(SITE_WS_API_PATH);
$loader->useIncludePath(true);

$loader->register();
