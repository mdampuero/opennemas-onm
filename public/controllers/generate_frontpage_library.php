#!/usr/bin/php5
<?php
/**
 * Start up and setup the app
*/
$_SERVER['SERVER_NAME'] ='cronicasdelaemigracion.com';
$_SERVER['REQUEST_URI'] ='/';
require_once('../bootstrap.php');

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpages');

$menuItems = Menu::renderMenu('frontpage');
$date =  new DateTime();
$directoryDate = $date->format("/Y/m/d/");
$basePath = MEDIA_PATH.'/library'.$directoryDate;
if ( !file_exists($basePath) ) {
    mkdir($basePath, 0777, true);
}
require_once "index_advertisement.php";

foreach ($menuItems->items as $item) {
    $subcategory_name ='';
    $cache_page = 0;
    $category_name = $item->link;

    $cacheID = $tpl->generateCacheId($category_name, $subcategory_name, $cache_page);

    if(($tpl->caching == 1)
       && $tpl->isCached('frontpage/frontpage.tpl', $cacheID))
    {
        //get from cache

        $htmlOut = $tpl->fetch('frontpage/frontpage.tpl', $cacheID);

    } else {
        //get from a index

        $url = SITE_URL."seccion/".$category_name."/";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
        $htmlOut = curl_exec($ch);
        curl_close($ch);

    }

    $newFile =  $basePath.$category_name.".html";

    $result = file_put_contents($newFile, $htmlOut);


}

