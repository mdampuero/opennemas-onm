<?php
defined('SITE_URL') or die('Direct access is forbidden');


/**
 * Fetch HTTP vars
 */
$category_name = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

$ccm = ContentCategoryManager::get_instance();
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

// Load 1-16 banners and use cache to performance
//$banners = $advertisement->getAdvertisements(range(1, 16), $category); // 4,9 unused
$banners = $advertisement->getAdvertisements(
    array(1,2, 3,4, 5,6, 11,12,13,14,15,16, 21,22,24,25, 31,32,33,34,35,36,103,105, 9, 91, 92),
    $category
);

$cm = new ContentManager();
$banners = $cm->getInTime($banners);
//$advertisement->renderMultiple($banners, &$tpl);
$advertisement->renderMultiple($banners, $advertisement);

// Get intersticial banner
$intersticial = $advertisement->getIntersticial(50, $category);
if (!empty($intersticial)) {
    $advertisement->renderMultiple(array($intersticial), $advertisement);
}

