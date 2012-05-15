#!/usr/bin/php5
<?php

/**
 * Start up and setup the app
*/
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
if( !file_exists($basePath) ) {
    mkdir($basePath, 0777, true);
}
require_once "index_advertisement.php";

foreach($menuItems->items as $item) {
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

    $actualCategory = $category_name;
    $actualCategoryId = $actual_category_id = $ccm->get_id($actualCategory);
    $tpl->assign(array(
        'actual_category_id' => $actualCategoryId,
        'actual_category_title' => $ccm->get_title($category_name),
        'category_name' => $category_name,
        'actual_category' => $actualCategory
    ));


    $cm = new ContentManager;

    $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);

    // Filter articles if some of them has time scheduling and sort them by position
    $contentsInHomepage = $cm->getInTime($contentsInHomepage);
    $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');

    /***** GET ALL FRONTPAGE'S IMAGES *******/
    $imageIdsList = array();
    foreach ($contentsInHomepage as $content) {
        if (isset($content->img1)) {
            $imageIdsList []= $content->img1;
        }
    }

    if (count($imageIdsList) > 0) {
        $imageList = $cm->find('Photo', 'pk_content IN ('. implode(',', $imageIdsList) .')');
    } else {
        $imageList = array();
    }

    // Overloading information for contents
    foreach ($contentsInHomepage as &$content) {

        // Load category related information
        $content->category_name  = $content->loadCategoryName($content->id);
        $content->category_title = $content->loadCategoryTitle($content->id);

        // Load attached and related contents from array
        $content->loadFrontpageImageFromHydratedArray($imageList)
                ->loadAttachedVideo()
                ->loadRelatedContents();
    }

    $tpl->assign('column', $contentsInHomepage);

    $htmlOut = $tpl->fetch('frontpage/frontpage.tpl', $cacheID);

    }

    $newFile =  $basePath.$category_name.".html";

    $result = file_put_contents($newFile, $htmlOut);


}

