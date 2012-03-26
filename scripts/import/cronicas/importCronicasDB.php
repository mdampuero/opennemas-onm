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
set_include_path(
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../vendor/').PATH_SEPARATOR.
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../public/libs/').PATH_SEPARATOR.
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../public/core/').PATH_SEPARATOR.
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../public/models/').PATH_SEPARATOR.
                    get_include_path()
                );
require 'db-config.inc.php';
require '../../../config/config.inc.php';

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../'));

 //require realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../vendor/').'/adodb5/adodb.inc.php';


// Paths settings
define('SITE_PATH',        realpath(APPLICATION_PATH. DIRECTORY_SEPARATOR . "public" ).DIRECTORY_SEPARATOR);
define('SITE_LIBS_PATH',   realpath(SITE_PATH . "libs") . DIRECTORY_SEPARATOR);
define('SITE_CORE_PATH',   realpath(SITE_PATH.DIRECTORY_SEPARATOR."core").DIRECTORY_SEPARATOR);
define('SITE_VENDOR_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."vendor").DIRECTORY_SEPARATOR);
define('SITE_MODELS_PATH', realpath(SITE_PATH.DIRECTORY_SEPARATOR."models").DIRECTORY_SEPARATOR);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    SITE_CORE_PATH, SITE_LIBS_PATH, SITE_VENDOR_PATH, SITE_MODELS_PATH, get_include_path(),
)));


require SITE_PATH.'/autoload.php';
\Application::initAutoloader('*');

$app = \Application::load();


if(!defined(INSTANCE_MEDIA) )
    define('INSTANCE_MEDIA', SITE_PATH.'media/images');

/**
 * General configurations
*/

$cronicasImporter = new CronicasToOnm($configOldDB,$configNewDB);

$helper = new CronicasHelper();
$helper->clearExamples(); //delete example contents
$helper->sqlExecute();

$cronicasImporter->importCategories();

<<<<<<< Updated upstream
$cronicasImporter->importImages();

$cronicasImporter->importArticles();
=======
//$cronicasImporter->importImagesArticles();

$limit = ' LIMIT 11000, 11200';
$cronicasImporter->importArticles($limit);
>>>>>>> Stashed changes

//$cronicasImporter->importRelatedContents();

//$cronicasImporter->importAuthorsOpinion();

/*
$cronicasImporter->importOpinions();

<<<<<<< Updated upstream
$cronicasImporter->importSpecials();
=======
$cronicasImporter->importOtherImages();

$cronicasImporter->importGalleries();
>>>>>>> Stashed changes

$cronicasImporter->importAlbums();
 *
 * $cronicasImporter->importSpecials();

$cronicasImporter->importLetters();

$cronicasImporter->importAdvertisements();

printf("OpenNemas database is ok for Cronicas \n");

$helper->printResults();
