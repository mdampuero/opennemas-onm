<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;

// Start up and setup the app
require_once '../bootstrap.php';

// Redirect Mobile browsers to mobile site unless a cookie exists.
$app->mobileRouter();

// Fetch HTTP variables
$category_name    = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', '', FILTER_SANITIZE_STRING);
$cache_page       = $request->query->filter('page', 0, FILTER_VALIDATE_INT);

// Setup view
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpages');
$cacheID = $tpl->generateCacheId($category_name, $subcategory_name, 0 /*$cache_page*/);

$actualCategory = (empty($subcategory_name))? $category_name : $subcategory_name;
$tpl->assign(
    array(
        'category_name'   => $category_name,
        'actual_category' => $actualCategory
    )
);

// Fetch information for Advertisements
require_once 'index_advertisement.php';

// Avoid to run the entire app logic if is available a cache for this page
if (
    $tpl->caching == 0
    || !$tpl->isCached('frontpage/frontpage.tpl', $cacheID)
) {

    /**
     * Init the Content and Database object
    */
    $ccm = ContentCategoryManager::get_instance();

    // If no home category name
    if ($category_name != 'home') {
        // Redirect to home page if the desired category doesn't exist
        if (empty($category_name) || !$ccm->exists($category_name)) {
            $output = $tpl->fetch('frontpage/not_found.tpl');
            $response = new Response($output, 404, array('content-type' => 'text/html'));
            $response->send();
            exit(0);
        }
    }


    $actualCategoryId = $actual_category_id = $ccm->get_id($actualCategory);
    $tpl->assign(
        array(
            'actual_category_id' => $actualCategoryId,
            'actual_category_title' => $ccm->get_title($category_name),
        )
    );

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
                ->loadRelatedContents($category_name);
    }
    $tpl->assign('column', $contentsInHomepage);

    $layout = s::get('frontpage_layout_'.$actualCategoryId, 'default');
    $layoutFile = 'layouts/'.$layout.'.tpl';

    $tpl->assign('layoutFile', $layoutFile);
}

$tpl->display('frontpage/frontpage.tpl', $cacheID);

