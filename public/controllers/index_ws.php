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
//$app->mobileRouter();

// Fetch HTTP variables
$category_name    = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', '', FILTER_SANITIZE_STRING);
$cache_page       = $request->query->filter('page', 0, FILTER_VALIDATE_INT);

// Setup view
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpages');
$cacheID = $tpl->generateCacheId('sync'.$category_name, $subcategory_name, 0);

// Fetch advertisement information from local
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
    $cm = new ContentManager;

    // Get sync params
    $wsUrl = '';
    $syncParams = s::get('sync_params');
    foreach ($syncParams as $siteUrl => $categoriesToSync) {
        foreach ($categoriesToSync as $value) {
            if (preg_match('/'.$category_name.'/i', $value)) {
                $wsUrl = $siteUrl;
            }
        }
    }

    // Check if category exists
    $existsCategory = $cm->getUrlContent($wsUrl.'/ws/categories/exist/'.$category_name);

    // If no home category name
    if ($category_name != 'home') {
        // Redirect to home page if the desired category doesn't exist
        if (empty($category_name) || !$existsCategory ) {
            $output = $tpl->fetch('frontpage/not_found.tpl');
            $response = new Response($output, 404, array('content-type' => 'text/html'));
            $response->send();
            exit(0);
        }
    }

    $actualCategory = (empty($subcategory_name))? $category_name : $subcategory_name;
    // Get category id correspondence
    $wsActualCategoryId = $cm->getUrlContent($wsUrl.'/ws/categories/id/'.$category_name);
    $tpl->assign(
        array(
            'category_name' => $category_name,
            'actual_category' => $actualCategory,
            'actual_category_id' => $wsActualCategoryId,
            'actual_category_title' => $ccm->get_title($category_name),
        )
    );


    // Get all contents for this frontpage
    $allContentsInHomepage = $cm->getUrlContent(
        $wsUrl.'/ws/frontpages/allcontent/'.$category_name,
        true
    );

    $tpl->assign('column', unserialize($allContentsInHomepage));

    // Fetch layout for categories
    $layout = $cm->getUrlContent($wsUrl.'/ws/categories/layout/'.$category_name, true);
    $layoutFile = 'layouts/'.$layout.'.tpl';

    $tpl->assign('layoutFile', $layoutFile);

}

$tpl->display('frontpage/frontpage.tpl', $cacheID);

