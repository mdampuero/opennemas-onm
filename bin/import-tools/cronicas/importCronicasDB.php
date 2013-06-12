#!/usr/bin/php5
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of refactorize
 *
 * @author sandra
 */


printf("Welcome to OpenNemas database Importer \n");

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
define('CACHE_PREFIX', 'cronicas-importer');

require 'cronicas-config.inc.php';

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
/**
 * General configurations
*/


$cronicasImporter = new CronicasToOnm($configOldDB,$configNewDB);

//$cronicasImporter->helper->sqlExecute(); // create translated tables
//$cronicasImporter->helper->sqlClearData(); //delete old data in tables


$cronicasImporter->importCategories();
/*
$cronicasImporter->importAuthorsOpinion();

$cronicasImporter->importPhotoAuthorsOpinion();
printf("Check author names (Problem with similar name author)");
exit();


$limit = ' LIMIT 0, 18000';
$cronicasImporter->importImagesArticles();
$cronicasImporter->importArticles($limit);

$cronicasImporter->importOpinions();

$cronicasImporter->importLetters();

$cronicasImporter->importSpecials();

$cronicasImporter->importGalleries();

$cronicasImporter->importAlbums();

$cronicasImporter->importAdvertisements();

$cronicasImporter->importAttachments();

$cronicasImporter->importRelatedContents();


$cronicasImporter->updateFrontpageArticles();

printf("OpenNemas database is ok for Cronicas \n");

$cronicasImporter->helper->printResults();
*/

////UPDATE `contents` SET available=1  WHERE `fk_content_type` =8


$where = 'AND `in_litter` = 0 AND `available` = 0';
//$cronicasImporter->exportArticles($where);

$cronicasImporter->processJSONFile('exporterLog.txt');
