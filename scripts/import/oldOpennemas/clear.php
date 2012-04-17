#!/usr/bin/php5
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Save in log file inconsistent data & delete from database
 *
 * @author sandra
 */


printf("  \n");

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



 //require realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../vendor/').'/adodb5/adodb.inc.php';
require  'adodb5/adodb.inc.php';


/**
 * General configurations
*/
require 'db-config.inc.php';
require 'broom.php';

$broom = new Broom($config);

$broom->writeDataInLog(); //Save in log possible information of tribuna
printf("log is written ok \n");

$ok=$broom->clearExecute();

if ($ok) {
    printf("OpenNemas database is cleared \n");
} else {
    printf("Sorry database isn't cleared check line 50 is commented \n");
}