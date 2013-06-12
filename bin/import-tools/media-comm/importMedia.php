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


printf("Welcome to OpenNemas Media Importer \n");

/**
 * Setting up the import application
*/

error_reporting(E_ALL ^ E_NOTICE);


define('DS', '/');

define('APPLICATION_PATH', realpath(__DIR__.'/../../../'));
define('SITE_PATH', realpath(APPLICATION_PATH. DIRECTORY_SEPARATOR ."public").DIRECTORY_SEPARATOR);

define('CACHE_PREFIX', 'media-importer');


require 'libs/configuration.inc.php';

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

$importer = new MediaImporter($configDB);

$importer->loadCategories();


$importer->getImages();


printf("OpenNemas ". INSTANCE_UNIQUE_NAME ." media is ok  \n");

