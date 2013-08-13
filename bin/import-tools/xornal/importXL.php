#!/usr/bin/env php
<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Sandra Pereira <sandra@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

printf("Welcome to OpenNemas data importer from WordPress\n");

/**
 * Setting up the import application
*/
error_reporting(E_ALL ^ E_NOTICE);

define('SYS_LOG_PATH', realpath(__DIR__.'/../../../tmp/logs/'));


/**
 * General configurations
*/
require 'db-config.inc.php';

require_once __DIR__.'/../../../app/autoload.php';
require_once __DIR__.'/../../../app/container.php';

define('INSTANCE_UNIQUE_NAME', 'mundiario');
define('IMG_DIR', 'images');
define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME);


require 'import-contents.php';


print_r(" \n You will import opinions {$topic} \n");

$refactor = new importContents($config_newDB, $config_oldDB);
// $refactor->importOpinions($oldId);

/* print_r(" \n  You will import articles by id {$oldId} \n");
*/
$refactor->importArticles($oldId, $newID, $topic);
/*
print_r(" \n You will import articles by topic {$topic} \n");
//$refactor->importArticles($topic);
*/



 printf("OpenNemas database is ok \n");
