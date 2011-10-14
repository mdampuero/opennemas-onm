<?php

printf("Welcome to OpenNemas data importer from EditMaker\n");

/**
 * Setting up the import application
*/
error_reporting(E_ALL ^ E_NOTICE);
set_include_path(
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'libs/').PATH_SEPARATOR.
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../public/libs/').PATH_SEPARATOR.
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../public/core/').PATH_SEPARATOR.
                    get_include_path()
                );

/**
 * Initializing essential classes
*/
require 'import_helper.php';
require '../config/config.inc.php';
$ihelper = new ImportHelper(dirname(__FILE__) . '/import.log');

require SITE_LIBS_PATH.'/adodb5/adodb.inc.php';
Application::importLibs('*');
$app = Application::load();

/**
 * General configurations
*/
require 'editmaker-config.inc.php';

function getOriginalAuthors()
{

}

function getNewAuthors()
{

}
