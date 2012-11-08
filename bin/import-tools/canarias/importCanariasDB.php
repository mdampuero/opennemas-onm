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


$option = 'articles';

switch ($option) {
    case 'clear':
        $importer->helper->sqlClearData(); //delete old data in tables
        $importer->helper->clearLog();

        break;
    case 'clearCategories':
        $importer->helper->clearCategories(); //delete old categories
        $importer->helper->sqlClearData(); //delete old data in tables
        $importer->helper->clearLog();
        echo 'clear completele';

        break;
    case 'ayuntamientos':
        $importer->importAyuntamientos();

        break;
    case 'opinion':
        $importer->helper->log('IMPORTING AUTHORS AND OPINIONS');
        $importer->createDefaultAuthors();
        $importer->importAuthorsOpinion();
        $importer->importOpinions();
        break;
    case 'articles':
        $importer->helper->log('IMPORTING ARTICLES AND IMAGES');
      /*  $importer->importHemeroteca();
        $importer->importHemerotecaTopSecret();
        $importer->importTopSecret();
        $importer->importFauna(); */
        $importer->importImagesArticles();
        $importer->importArticles();

        break;
    case 'other-contents':
        $importer->helper->log("\n IMPORTING OTHER CONTENTS \n");
        $importer->importLetters();

        $importer->importImagesHumor();

        $importer->importAttachments();

        $importer->importAlbums();
        /*
        // $importer->importComments();
         //$importer->importRelatedContents();
        */
        break;
    default:
        # code...
        break;
}

$importer->importCategories();

printf("OpenNemas database is ok for Canarias Ahora \n");

$importer->helper->printResults();
