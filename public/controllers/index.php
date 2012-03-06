<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
// Start up and setup the app
require_once '../bootstrap.php';

// Redirect Mobile browsers to mobile site unless a cookie exists.
$app->mobileRouter();

// Fetch HTTP variables
$category_name    = filter_input(
    INPUT_GET, 'category_name', FILTER_SANITIZE_STRING,
    array('options' => array('default' => 'home'))
);
$subcategory_name = filter_input(INPUT_GET,'subcategory_name',FILTER_SANITIZE_STRING);

// Setup view
$tpl     = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpages');
$cacheID = $tpl->generateCacheId($category_name, $subcategory_name, 0 /*$cache_page*/);

// Fetch information for Advertisements
require_once "index_advertisement.php";

// Avoid to run the entire app logic if is available a cache for this page
if (
    $tpl->caching == 0
    || !$tpl->isCached('frontpage/frontpage.tpl', $cacheID)
) {

    // Initialize the Content and Database object
    $ccm = ContentCategoryManager::get_instance();
    list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
    $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
    $section = (is_null($section))? 'home': $section;

    $tpl->loadConfigOrDefault('template.conf', $section);
    unset($section);

    // If no home category name
    if ($category_name != 'home') {

        // Redirect to home page if the desired category doesn't
        // exist or  is empty this is a home page
        if (empty($category_name) || !$ccm->exists($category_name)) {
            Application::forward301('/');
        } else {
            // If there is no any article in a category forward into the first subcategory
            if ($ccm->isEmpty($category_name) && !isset($subcategory_name)) {
                $subcategory_name = $ccm->get_first_subcategory($ccm->get_id($category_name));

                $forwardUrl = '/';
                if (!empty($subcategory_name)) {
                    $forwardUrl = '/seccion/'.$category_name.'/'.$subcategory_name.'/';
                }
                Application::forward301($forwardUrl);

            } else {
                $category = $ccm->get_id($category_name);
            }

        }

        if (isset($subcategory_name) && !empty($subcategory_name)) {
            if (!$ccm->exists($subcategory_name)) {
                Application::forward301('/');
            } else {
                $subcategory = $ccm->get_id($subcategory_name);
            }
        }

    }

    $actual_category = (!isset($subcategory_name))? $category_name : $subcategory_name;

    $tpl->assign('actual_category', $actual_category);
    $actualCategoryId = $ccm->get_id($actual_category);

    require_once "index_sections.php";

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

    $column = array();
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

    // Fetch information for Static Pages
    //TODO: Move to a widget. Used in all templates
    require_once "widget_static_pages.php";


} // $tpl->is_cached('index.tpl')
$tpl->display('frontpage/frontpage.tpl', $cacheID);
