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
$ext = $request->query->filter('ext', 0, FILTER_VALIDATE_INT);

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

    // Check if category exists with file_get_contents
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
    $actualCategoryId = $actual_category_id = $ccm->get_id($actualCategory);
    $tpl->assign(
        array(
            'category_name' => $category_name,
            'actual_category' => $actualCategory,
            'actual_category_id' => $actualCategoryId,
            'actual_category_title' => $ccm->get_title($category_name),
        )
    );

    // Get category id correspondence
    $wsActualCategoryId = $cm->getUrlContent($wsUrl.'/ws/categories/id/'.$category_name);

    /*
    // Fetch information for Advertisements from Web service
    $ads = json_decode(file_get_contents($wsUrl.'/ws/ads/frontpage/'.$wsActualCategoryId));

    $intersticial = $ads[0];
    $banners = $ads[1];

    //Render ads
    $advertisement = Advertisement::getInstance();
    $advertisement->renderMultiple($banners, $advertisement,$wsUrl);

    // Render intersticial banner
    if (!empty($intersticial)) {
        $advertisement->renderMultiple(array($intersticial), $advertisement,$wsUrl);
    }
    */

    $allContentsInHomepage = $cm->getUrlContent(
        $wsUrl.'/ws/categories/allcontent/'.$wsActualCategoryId,
        true
    );

    $contentsInHomepage = array();
    foreach ($allContentsInHomepage as $item) {
        $contentType = $cm->getUrlContent(
            $wsUrl.'/ws/contents/contenttype/'.(int)$item->fk_content_type
        );
        $contentType = str_replace('"', '', $contentType);
        $content = new $contentType();
        $content->load($item);
        $contentsInHomepage[] = $content;
    }

    // Filter articles if some of them has time scheduling and sort them by position
    $contentsInHomepage = $cm->getInTime($contentsInHomepage);
    $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');

    // Get all frontpages images
    $imageList = array();
    foreach ($contentsInHomepage as $content) {
        if (isset($content->img1) && $content->img1 != 0) {
            $image = $cm->getUrlContent($wsUrl.'/ws/images/id/'.(int)$content->img1, true);
            if (!empty($image)) {
                $image->media_url = $cm->getUrlContent($wsUrl.'/ws/instances/mediaurl/', true);
                $imageList []= $image;
            }
        }
    }

    // Overloading information for contents
    foreach ($contentsInHomepage as &$content) {

        // Load category related information
        $content->category_name  = $category_name;
        $content->category_title = $cm->getUrlContent(
            $wsUrl.'/ws/contents/loadcategorytitle/'.$content->id,
            true
        );

        // Get author_name_slug for opinions
        if ($content->content_type == '4') {
            if ($content->type_opinion == 1) {
                $content->author_name_slug = 'editorial';
            } elseif ($content->type_opinion == 2) {
                $content->author_name_slug = 'director';
            } else {
                $content->author_name_slug = StringUtils::get_title($content->name);
            }
            // Generate opinion uri with author_name_slug
            $content->uri = preg_replace('@author@', $content->author_name_slug, $content->uri);
        }

        //Change uri for href links except widgets
        if ($content->content_type != 'Widget') {
            $content->uri = "ext".$content->uri;
        }

        // Load attached  from array
        $content->loadFrontpageImageFromHydratedArray($imageList)
                ->loadAttachedVideo();

        // Load related contents from ws
        $content->related_contents = $cm->getUrlContent(
            $wsUrl.'/ws/articles/lists/related/'.$content->id,
            true
        );

        // Generate uri for related content
        foreach ($content->related_contents as &$item) {
            $contentType = $cm->getUrlContent(
                $wsUrl.'/ws/contents/contenttype/'.(int) $item->fk_content_type,
                true
            );
            $contentType = str_replace('"', '', $contentType);
            $contentRelated = new $contentType();
            $contentRelated->load($item);
            $contentRelated->category_name = $category_name;
            // Generate content uri if it's not an attachment
            if ($item->fk_content_type != 3) {
                $contentRelated->uri = "ext".$contentRelated->uri;
            } else {
                // Get instance media
                $basePath = $cm->getUrlContent(
                    $wsUrl.'/ws/instances/instancemedia/',
                    true
                );
                // Get file path for attachments
                $filePath = $cm->getUrlContent(
                    $wsUrl.'/ws/contents/filepath/'.(int)$contentRelated->id,
                    true
                );
                // Compose the full url to the file
                $contentRelated->fullFilePath = $basePath.FILE_DIR.$filePath;
            }

            $item = $contentRelated;
        }
    }

    $tpl->assign('column', $contentsInHomepage);

    // Fetch layout for categories
    $layout = $cm->getUrlContent($wsUrl.'/ws/categories/layout/'.$category_name, true);
    $layoutFile = 'layouts/'.$layout.'.tpl';

    $tpl->assign('layoutFile', $layoutFile);

}

$tpl->display('frontpage/frontpage.tpl', $cacheID);

