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

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            SITE_CORE_PATH,
            SITE_VENDOR_PATH,
            SITE_MODELS_PATH,
            APP_PATH,
            SRC_PATH,
            SITE_WS_API_PATH,
            get_include_path(),
        )
    )
);
define('INSTALLATION_HASH', substr(hash('md5', APPLICATION_PATH), 0, 8));

require SITE_VENDOR_PATH.'/autoload.php';
require_once SITE_VENDOR_PATH.'functions.php';
require_once SITE_VENDOR_PATH.'/adodb5/adodb.inc.php';
require_once SITE_VENDOR_PATH.'/Pager/Pager.php';
require_once SITE_VENDOR_PATH.'/smarty/smarty-legacy/Smarty.class.php';
require_once SITE_VENDOR_PATH.'/Template.php';

require_once __DIR__.'/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

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
$loader->registerNamespaceFallback(SITE_MODELS_PATH);
$loader->registerNamespaceFallback(SITE_WS_API_PATH);
$loader->useIncludePath(true);

$loader->register();
