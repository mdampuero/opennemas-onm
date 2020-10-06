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
define('APP_PATH', APPLICATION_PATH . '/app/');

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

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('SYS_LOG_PATH', realpath(SITE_PATH . DS . '../tmp/logs'));

$serverName = array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : 'console';
define('SITE', $serverName);
define('ADMIN_DIR', 'admin');
define('SITE_URL', $protocol . SITE);

define('FILE_DIR', 'files');
define('ITEMS_PAGE', '20'); // TODO: delete from application

define('STATIC_PAGE_PATH', 'estaticas');

// Backup paths
define('BACKUP_PATH', SITE_PATH . DS . '../tmp/backups');

$maxUpload          = (int) (ini_get('upload_max_filesize'));
$maxPost            = (int) (ini_get('post_max_size'));
$memoryLimit        = (int) (ini_get('memory_limit'));
$maxAllowedFileSize = min($maxUpload, $maxPost, $memoryLimit) * pow(1024, 2);
define('MAX_UPLOAD_FILE', $maxAllowedFileSize);

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
