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

define('APC_PREFIX', 'onm-importer');

require 'libs/onm-config.inc.php';

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

$importer = new ContentsImporter($configOldDB, $configNewDB);



switch ($argv[1]) {
    case 'opinions':
        $importer->helper->log('IMPORTING   OPINIONS');
        $importer->importOpinions();

        break;
    case 'update-authors':
        $importer->helper->log('UPDATING   OPINIONS');
        $importer->updateOpinionAuthors();

        break;
    case 'clear':
        $importer->helper->sqlClear();

        break;
    case 'clear-opinion':
        $importer->helper->sqlClearOpinions();

        break;
    case 'create-tables':
        $importer->helper->sqlExecute();

        break;
    case 'clear-contentsCategories':
        $importer->helper->clearContentsCategoriesTable();

        break;
    default:
        # code...

        break;
}

echo "\nYou can use options: \n -clear: clear importer tables, \n-clear-opinion: clear old opinions, ".
    "\n-create-tables: create importer tables, \n- clear-contentsCategories: clear contents-categories table ".
    "\n-opinions: import opinions, \n-update-authors: update author id in opinios \n";


$importer->helper->printResults();
