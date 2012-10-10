<?php

/**
 * General configurations
*/

$configOldDB = array(
                          'host' => 'localhost',
                          'database' => 'ant-canariasAhora',
                          'user' => 'root',
                          'password' => 'root' ,
                          'type' => 'mysql' ,
                          );

$configNewDB = array(
                          'host' => 'localhost',
                          'database' => 'c-canariasaho',
                          'user' => 'root',
                          'password' => 'root' ,
                          'type' => 'mysql' ,
                          );


//define internal name instance from manager
define('INSTANCE_UNIQUE_NAME', 'canariasAhora');

//galimundo id.
define('USER_ID', '135');

define('OLD_MEDIA', '/var/www/canariasSw/canariasahora_20');

defined('MEDIA_PATH')
    || define('MEDIA_PATH', SITE_PATH. 'media');

defined('IMG_DIR')
    || define('IMG_DIR', 'canariasahora/images');


defined('FILE_DIR')
    || define('FILE_DIR', 'canariasahora/files');

