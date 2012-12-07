<?php

/**
 * General configurations
*/

$configOldDB = array(
                          'host' => 'localhost',
                          //'database' => 'ant-canariasAhora',
                          //'database' => 'ant-fuerteventura',
                          'database' => 'ant-lanzarote',
                          //'database' => 'ant-lapalma',
                          'user' => 'root',
                          'password' => 'root' ,
                          'type' => 'mysql' ,
                          );

$configNewDB = array(
                          'host' => 'localhost',
                          //'database' => 'c-canariasaho',
                          //'database' => 'c-fuerteventu',
                          'database' => 'c-lanzaroteah',
                          //'database' => 'c-lapalmaahor',
                          'user' => 'root',
                          'password' => 'root' ,
                          'type' => 'mysql' ,
                          );


//define internal name instance from manager
//define('INSTANCE_UNIQUE_NAME', 'canariasAhora');
//define('INSTANCE_UNIQUE_NAME', 'fuerteventuraahora');
define('INSTANCE_UNIQUE_NAME', 'lanzaroteahora');
//define('INSTANCE_UNIQUE_NAME', 'lapalmaahora');
//galimundo id.
define('USER_ID', '135');

//define('OLD_MEDIA', '/var/www/canariasSw/canariasahora_20');
//define('OLD_MEDIA', '/var/www/canariasSw/fuerteventura');
define('OLD_MEDIA', '/var/www/canariasSw/lanzarote');
//define('OLD_MEDIA', '/var/www/canariasSw/lapalma');

defined('MEDIA_PATH')
    || define('MEDIA_PATH', SITE_PATH. 'media');

defined('IMG_DIR')
    || define('IMG_DIR', INSTANCE_UNIQUE_NAME.'/images');


defined('FILE_DIR')
    || define('FILE_DIR', INSTANCE_UNIQUE_NAME.'/files');

