#!/usr/bin/env php
<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Sandra Pereira <sandra@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

printf("Welcome to OpenNemas data importer from WordPress\n");

/**
 * Setting up the import application
*/
error_reporting(E_ALL ^ E_NOTICE);


define('DS', '/');

define('APPLICATION_PATH', realpath(__DIR__.'/../../../'));
define('SITE_PATH',        realpath(APPLICATION_PATH. DIRECTORY_SEPARATOR . "public" ).DIRECTORY_SEPARATOR);
define('SITE_LIBS_PATH',   realpath(SITE_PATH . "libs") . DIRECTORY_SEPARATOR);
define('SITE_CORE_PATH',   realpath(SITE_PATH.DIRECTORY_SEPARATOR."core").DIRECTORY_SEPARATOR);
define('SITE_VENDOR_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."vendor").DIRECTORY_SEPARATOR);
define('SITE_MODELS_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."models").DIRECTORY_SEPARATOR);
define('CACHE_PREFIX', 'wordpress-importer');



// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array( __DIR__.'/libs/',
    SITE_CORE_PATH, SITE_LIBS_PATH, SITE_VENDOR_PATH, SITE_MODELS_PATH,
    '/usr/share/php/',    get_include_path(),
)));
/**
 * Initializing essential classes
*/
require SITE_VENDOR_PATH.'/adodb5/adodb.inc.php';

require SITE_PATH.'../app/autoload.php';
Application::initAutoloader();



require SITE_PATH.'../config/config.inc.php';

require 'libs/WordPressToOnm.php';
//$ihelper = new wpHelper(dirname(__FILE__) . '/import.log');

/**
 * General configurations
*/

$migrationHandler = new WordPressToOnm();

$migrationHandler->importCategories();

$migrationHandler->importOpinions();

$migrationHandler->importImages();

$migrationHandler->importArticles();

$migrationHandler->importComments();

$migrationHandler->printResults();


