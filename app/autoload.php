<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// Define path to application directory
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

$rootDir = empty($_SERVER['DOCUMENT_ROOT']) ? __DIR__ : $_SERVER['DOCUMENT_ROOT'];
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', dirname($rootDir));

// Paths settings
define('SRC_PATH', APPLICATION_PATH . '/src/');
define('SITE_PATH', APPLICATION_PATH . '/public/');
define('SITE_VENDOR_PATH', APPLICATION_PATH . '/vendor/');
define('SITE_LIBS_PATH', APPLICATION_PATH . '/libs/');
define('SITE_CORE_PATH', SITE_LIBS_PATH . '/core/');
define('SITE_MODELS_PATH', APPLICATION_PATH . '/app/models/');
define('APP_PATH', APPLICATION_PATH . '/app/');
define('SITE_WS_API_PATH', SRC_PATH . '/WebService/Handlers/');

define('SMARTY_DIR', SITE_VENDOR_PATH . 'smarty/smarty/libs/');
define('INSTALLATION_HASH', substr(hash('md5', APPLICATION_PATH), 0, 8));

if (file_exists(APPLICATION_PATH . '/.deploy.php')) {
    include_once APPLICATION_PATH . '/.deploy.php';
}

if (!defined('DEPLOYED_AT')) {
    define('DEPLOYED_AT', '00000000000000');
}

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || (array_key_exists('SERVER_PORT', $_SERVER) && $_SERVER['SERVER_PORT'] == 443)
    || (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) {
    $protocol = 'https://';
} else {
    $protocol = 'http://';
}

define('SS', '/');

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('SYS_LOG_PATH', realpath(SITE_PATH . DS . '../tmp/logs'));

$serverName = array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : 'console';
define('SITE', $serverName);
define('BASE_URL', '/');
define('ADMIN_DIR', 'admin');
define('SITE_URL', $protocol . SITE . BASE_URL);
define('SITE_URL_ADMIN', SITE_URL . ADMIN_DIR);

define('SYS_NAME_GROUP_ADMIN', 'Administrador');

define('IMG_DIR', 'images');
define('FILE_DIR', 'files');
define('ADS_DIR', 'advertisements');
define('OPINION_DIR', 'opinions');

define('TEMPLATE_MANAGER', 'manager');

define('ITEMS_PAGE', '20'); // TODO: delete from application

define('TEMPLATE_ADMIN', 'admin');
define('TEMPLATE_ADMIN_PATH', SITE_PATH . '/themes/' . TEMPLATE_ADMIN . SS);
define('TEMPLATE_ADMIN_PATH_WEB', '/themes/' . TEMPLATE_ADMIN . SS);
define('TEMPLATE_ADMIN_URL', '/themes/' . TEMPLATE_ADMIN . SS);

define('STATIC_PAGE_PATH', 'estaticas');

// Backup paths
define('BACKUP_PATH', SITE_PATH . DS . '../tmp/backups');

$maxUpload          = (int) (ini_get('upload_max_filesize'));
$maxPost            = (int) (ini_get('post_max_size'));
$memoryLimit        = (int) (ini_get('memory_limit'));
$maxAllowedFileSize = min($maxUpload, $maxPost, $memoryLimit) * pow(1024, 2);
define('MAX_UPLOAD_FILE', $maxAllowedFileSize);

$commonCachepath = APPLICATION_PATH . '/tmp/compiles';
define('COMMON_CACHE_PATH', $commonCachepath);

mb_internal_encoding('UTF-8');

/**
 * @var $loader ClassLoader
 */
$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->add('Onm', SITE_LIBS_PATH);
$loader->setUseIncludePath(true);
$loader->register();

AnnotationRegistry::registerLoader([ $loader, 'loadClass' ]);
AnnotationReader::addGlobalIgnoredName('api');
AnnotationReader::addGlobalIgnoredName('apiName');
AnnotationReader::addGlobalIgnoredName('apiGroup');
AnnotationReader::addGlobalIgnoredName('apiParam');
AnnotationReader::addGlobalIgnoredName('apiSuccess');

return $loader;
