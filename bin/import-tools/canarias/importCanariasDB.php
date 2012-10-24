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
define('SITE_PATH', realpath(APPLICATION_PATH. DIRECTORY_SEPARATOR ."public").DIRECTORY_SEPARATOR);

define('APC_PREFIX', 'canarias-importer');

require 'libs/canariasImport-config.inc.php';

require SITE_PATH.'../app/autoload.php';

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            __DIR__.'/libs/',
            SITE_CORE_PATH,
            SITE_VENDOR_PATH,
            SITE_MODELS_PATH,
            '/usr/share/php/',
            get_include_path(),
        )
    )
);



$importer = new CanariasToOnm($configOldDB, $configNewDB);


//$importer->helper->sqlClearData(); //delete old data in tables
//$importer->helper->clearLog();

$importer->importCategories();

 /*
$importer->helper->log('IMPORTING AUTHORS AND OPINIONS');

$importer->importAuthorsOpinion();

$importer->importPhotoAuthorsOpinion();
printf("Check author names (Problem with similar name author)");

$importer->importOpinions();

$importer->helper->log('IMPORTING ARTICLES AND IMAGES');

/*$importer->importHemeroteca();
$importer->importHemerotecaTopSecret();

$importer->importAyuntamientos();

$importer->importTopSecret();

$importer->importFauna();



//$importer->importImagesArticles();
//$importer->importArticles();



$importer->helper->log("\n IMPORTING OTHER CONTENTS \n");
$importer->importLetters();

$importer->importImagesHumor();
*/
$importer->importAlbums();

//$importer->importAttachments();

//$importer->importComments();

//$importer->importRelatedContents();

/**/
printf("OpenNemas database is ok for Canarias Ahora \n");

$importer->helper->printResults();





