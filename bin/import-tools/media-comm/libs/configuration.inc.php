<?php

/**
 * General configurations
*/
define('OLD_MEDIA', '/home/sandra/clientsDoc/comm-media/');

define('INSTANCE_UNIQUE_NAME', 'lanzaroteahora');


$configDB = array(
                  'host'     => 'localhost',
                  'database' => 'c-lanzaroteah',
                  'user'     => 'root',
                  'password' => 'root' ,
                  'type'     => 'mysql' ,
                 );


define('USER_ID', '135');

defined('MEDIA_PATH')
    || define('MEDIA_PATH', SITE_PATH. 'media');

defined('IMG_DIR')
    || define('IMG_DIR', INSTANCE_UNIQUE_NAME.'/images');

