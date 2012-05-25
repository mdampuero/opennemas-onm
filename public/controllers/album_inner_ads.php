<?php
defined('SITE_URL') or die('Direct access is forbidden');

$ccm = ContentCategoryManager::get_instance();
$category_name='album';
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

// Load internal banners, principal banners (1,2,3,11,13) and use cache to performance
$banners = $advertisement->getAdvertisements(array(501, 502, 503, 509, 510), $category);

$cm = new ContentManager();
$banners = $cm->getInTime($banners);

$advertisement->render($banners, $advertisement);

$intersticial = $advertisement->getIntersticial(550, '$category');
if (!empty($intersticial)) {
    $advertisement->render(array($intersticial), $advertisement);
}
