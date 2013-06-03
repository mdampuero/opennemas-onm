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

define('CACHE_PREFIX', 'onm-importer');

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
    case 'import-opinions':
        $importer->helper->log('IMPORTING   OPINIONS');
        $importer->importOpinions();

        break;
    case 'import-newsstand':
        $importer->helper->log('IMPORTING NEWSSTAND');
        $importer->helper->sqlClearOpinions();
        $importer->importNewsstand();

        break;
    case 'update-authors':
        $importer->helper->log('UPDATING  AUTHOR OPINIONS');
        $importer->updateOpinionAuthors();

        break;
    case 'create-tables':
        $importer->helper->sqlExecute();

        break;
    case 'clear':
        $importer->helper->sqlClear();

        break;
    case 'clear-opinion':
        $importer->helper->sqlClearOpinions();

        break;
    case 'clear-newsstand':
        $importer->helper->sqlClearNewsstand();

        break;
    case 'clear-contentsCategories':
        $importer->helper->clearContentsCategoriesTable();

        break;
    default:
        # code...

        break;
}

echo "\nYou can use options: \n \n-create-tables: create importer tables, -clear: clear importer tables, ".
    " \n-clear-opinion: clear  opinions, \n-clear-newsstand: clear  newsstand, ".
    " \n- clear-contentsCategories: clear contents-categories table ".
    "\n-import-opinions: import opinions, \n-update-authors: update author id in opinios \n".
    "\n-import-newsstand: import kiosko files \n";


$importer->helper->printResults();
