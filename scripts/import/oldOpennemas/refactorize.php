#!/usr/bin/php5
<?php
/*
 *
 */

/**
 * Get content table refactorize id's and generate slug
 *
 * @author sandra
 */


printf("Welcome to OpenNemas database Refactorize \n");

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

/**
 * Initializing essential classes
*/

require  'adodb5/adodb.inc.php';



/**
 * General configurations
*/
require 'db-config.inc.php';
require 'refactor-ids.php';
//require 'string_utils.class.php';

 $refactor = new refactorIds($config);
 /*
 $refactor->executeSqlFile('changesForNT.sql');

 $refactor->executeSqlFile('createPrivileges.sql');

 $refactor->modifySchema(); //prepare tables

 $refactor->addMasterUsers(); // change admin to master Openhost's users

 $refactor->refactorDB(); // create new ids & slug

 $refactor->refactorSecondaryTables(); //change secondary table, example related_contents...

 $refactor->refactorImgTables(); //change id images in some tables
 */
 $refactor->updateFrontpageArticles(); // create new ids & slug

 printf(" \n OpenNemas database {$config['bd_database']} is ok \n ");
 printf("\n ---------Attention: you new change settings values & move media folder -------------- \n ");