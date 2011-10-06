#!/usr/bin/php5
<?php

require(dirname(__FILE__) . '/../www/config.inc.php');
require(dirname(__FILE__) . '/../www/core/application.class.php');
require(dirname(__FILE__) . '/../www/core/database_iterator.class.php');

require(dirname(__FILE__) . '/../www/core/content.class.php');
require(dirname(__FILE__) . '/../www/core/string_utils.class.php');

Application::import_libs('*');
$app = Application::load();

// DatabaseIterator
$dbIt = new DatabaseIterator($app->conn);


$contents   = $dbIt['contents'];


// Retrieve problematic rows
$rows = $contents->select()->where('title IS NOT NULL')->execute();
$i=0;
foreach($rows as $row) {                
    $previous = $row->slug;

    $row->slug = String_Utils::get_title( $row->title );
    $row->update();
    $i++;
    

    echo($row->slug.'\n');
}    

echo("\n\n");
echo("Procesados $i slugs.\n\n");
