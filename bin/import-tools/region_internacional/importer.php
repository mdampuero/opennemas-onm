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

require_once __DIR__.'/../../../app/autoload.php';

require SITE_PATH.'../app/autoload.php';

require SITE_PATH.'../config/config.inc.php';

require 'libs/RegionImporter.php';
//$ihelper = new wpHelper(dirname(__FILE__) . '/import.log');

/**
 * General configurations
*/

$migrationHandler = new RegionImporter();

$migrationHandler
    ->importCategories()
    ->importOpinions()
    ->importImages()
    ->importArticles()
    ->printResults();
