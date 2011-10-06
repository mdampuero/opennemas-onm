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


printf("  \n");

/**
 * Setting up the import application
*/
error_reporting(E_ALL ^ E_NOTICE);
set_include_path(
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../vendor/').PATH_SEPARATOR.
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../public/libs/').PATH_SEPARATOR.
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../public/core/').PATH_SEPARATOR.
                    realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../public/models/').PATH_SEPARATOR.
                    get_include_path()
                );

/**
 * Initializing essential classes
*/

require '../../config/config.inc.php';

 //require realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../vendor/').'/adodb5/adodb.inc.php';
require  'adodb5/adodb.inc.php';
 


//Application::importLibs('*');
//$app = Application::load();
 
/**
 * General configurations
*/
require 'db-config.inc.php';
require 'broom.php';


$broom = new Broom($config_editmaker);

$ok=$broom->sqlExecute();
 
if ($ok) {
    printf("OpenNemas database is cleared \n");
} else {
    printf("Sorry database isn't cleared \n");
}