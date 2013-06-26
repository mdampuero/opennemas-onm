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
require_once __DIR__.'/../../../app/container.php';

require 'libs/RegionImporter.php';
require __DIR__.'/../wordpress/libs/ImportHelper.php';

$migrationHandler = new RegionImporter();

$migrationHandler
    ->importCategories()
    ->loadCategories()
    // ->importArticles()
    // ->importMedia()
    // ->assignMediaToArticles();
    // ->importAuthors()
    // ->importOpinions()
    // ->printResults()
    ;
