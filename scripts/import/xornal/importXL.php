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
require 'db-config.inc.php';
require '../../../config/config.inc.php';

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../'));

 //require realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../vendor/').'/adodb5/adodb.inc.php';


// Paths settings
define('SITE_PATH',        realpath(APPLICATION_PATH. DIRECTORY_SEPARATOR . "public" ).DIRECTORY_SEPARATOR);
define('SITE_LIBS_PATH',   realpath(SITE_PATH . "libs") . DIRECTORY_SEPARATOR);
define('SITE_CORE_PATH',   realpath(SITE_PATH.DIRECTORY_SEPARATOR."core").DIRECTORY_SEPARATOR);
define('SITE_VENDOR_PATH', realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR."vendor").DIRECTORY_SEPARATOR);
define('SITE_MODELS_PATH', realpath(SITE_PATH.DIRECTORY_SEPARATOR."models").DIRECTORY_SEPARATOR);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    SITE_CORE_PATH, SITE_LIBS_PATH, SITE_VENDOR_PATH, SITE_MODELS_PATH, get_include_path(),
)));


 require_once('application.class.php');
 \Application::initAutoloader('*');

    $app = \Application::load();

 
if(!defined(INSTANCE_MEDIA) )
    define('INSTANCE_MEDIA', SITE_PATH.'media/images');

/**
 * General configurations
*/

require 'import-contents.php';
require 'string_utils.class.php';



print_r(" \n You will import opinions {$topic} \n");

$refactor = new importContents($config_newDB, $config_oldDB);
 $refactor->importOpinions($oldId);

/* print_r(" \n  You will import articles by id {$oldId} \n");
$refactor->importArticles($oldId, true);

print_r(" \n You will import articles by topic {$topic} \n");
//$refactor->importArticles($t/opic);
*/



 printf("OpenNemas database is ok \n");