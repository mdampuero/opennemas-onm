#!/usr/bin/php5
<?php
/*
 * Generate newspaper library by cron
 *
 */

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');


/**
 * Check general settings
 * if frontpage is displayed as list or as static file.
 */

$ccm = ContentCategoryManager::get_instance();
$cm = new ContentManager();
$fp = new Frontpage();

list($allcategorys, $subcat, $categoryData) = $ccm->getArraysMenu(0, 1);

foreach($allcategorys as $category) {

    $contents = $fp->getContentsPositionsInCategory($category->pk_content_category);
    $date =  date("Ymd");
    $_SESSION['userid'] = "0";
    $_SESSION['username'] = "frontpages_generator";
    if(!empty($contents)) {
        $values = array(
            'title' => "Newspaper library {$date} ",
            'category'=>$category->pk_content_category,
            'contents'=>$contents,
            'date' => $date
            );
        $fp->create($values);

        $msg = "Generate ok: {$category->pk_content_category} - {$date}  <br>";
        var_dump($msg);
    }
}

