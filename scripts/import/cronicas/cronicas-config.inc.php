<?php

/**
 * General configurations
*/

$configOldDB = array(
                          'host' => 'localhost',
                          'database' => 'webdev-cronicas',
                          'user' => 'root',
                          'password' => 'root' ,
                          'type' => 'mysql' ,
                          );

$configNewDB = array(
                          'host' => 'localhost',
                          'database' => 'onm-cronicas-bak1',
                          'user' => 'root',
                          'password' => 'root' ,
                          'type' => 'mysql' ,
                          );


//define internal name instance from manager
define('INSTANCE_UNIQUE_NAME', 'cronicas');

//galimundo id.
define('USER_ID','135');

define('OLD_MEDIA','/var/www/cronicas/media/');