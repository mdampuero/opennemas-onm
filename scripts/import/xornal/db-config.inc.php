<?php

 
$oldId ='58'; //author opinion id
$topic = '%_os_%_uis%_mez%'; //Sql patern Xosé Luis Gómez


/**
 * General configurations
*/
define('BD_TYPE', "mysqli");
define('BD_HOST' , "localhost");
define('BD_USER' , "root");
define('BD_PASS' , "root");
define('BD_DATABASE' , "onm-joseluisgo");

$config_oldDB = array(
                          'bd_host' => 'localhost',
                          'bd_database' => 'xornalcomdb',
                          'bd_user' => 'root',
                          'bd_pass' => 'root' ,
                          'bd_type' => 'mysql' ,
                          );

$config_newDB = array(
                          'bd_host' => 'localhost',
                          'bd_database' => BD_DATABASE,
                          'bd_user' => BD_USER,
                          'bd_pass' => BD_PASS ,
                          'bd_type' => BD_TYPE ,
                          );

 
  
 