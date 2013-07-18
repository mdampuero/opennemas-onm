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

define('CACHE_PREFIX', 'canarias-importer');

//require 'libs/lanzaroteImport-config.inc.php';
//require 'libs/fuerteventuraImport-config.inc.php';
//require 'libs/lapalmaImport-config.inc.php';
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



switch ($argv[1]) {
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
        $importer->importCategories();
        $importer->createDefaultAuthors();
        $importer->importAuthorsOpinion();
        $importer->importOpinions();
        break;
    case 'articles':
        $importer->helper->log('IMPORTING ARTICLES AND IMAGES');
        $importer->importCategories();
        $importer->importHemeroteca();
        $importer->importHemerotecaTopSecret();
        $importer->importTopSecret();
        $importer->importFauna();
        $importer->importImagesArticles();
        $importer->importArticles();

        break;
    case 'otherContents':
        $importer->helper->log("\n IMPORTING OTHER CONTENTS \n");

        $importer->importCategories();

        $importer->importImagesHumor();

        $importer->importLetters();

        $importer->importImagesHumor();

        $importer->importAttachments();

        $importer->importAlbums();

        $importer->importVideos();

        break;
    case 'polls':
        $importer->helper->log("\n IMPORTING OTHER CONTENTS \n");

        $importer->importCategories();

        $importer->importPolls();

        break;
    case 'all':
        $importer->helper->log("\n IMPORTING CONTENTS \n");

        $importer->createCategories();

        $importer->importAuthorsOpinion();

        $importer->importOpinions();

        $importer->importTopSecret();

        $importer->importImagesArticles();

        $importer->importArticles();

        $importer->importAttachments();

        $importer->importAlbums();

        $importer->importVideos();

        break;
    case 'updateMetadata':
        $importer->helper->log("\n UPDATING METADATA CONTENTS \n");

        $importer->getFilesData();

        $importer->updateContents();

        break;
    default:
        # code...

        break;
}

echo "You can use options: clear, clearCategories, opinion, ayuntamientos, articles, polls, otherContents, all \n";
printf("OpenNemas database is ok for Canarias Ahora \n");

$importer->helper->printResults();
