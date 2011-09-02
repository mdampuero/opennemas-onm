<?php
defined('SITE_URL') or die('Direct access is forbidden');

$ccm = ContentCategoryManager::get_instance();
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

// Load internal banners, principal banners (1,2,3,11,13) and use cache to performance
/* $banners = $advertisement->cache->getAdvertisements(array(1, 2, 3, 10, 12, 11, 13), $category); */
$banners = $advertisement->getAdvertisements(array(601, 602, 603, 605, 609, 610), $category);
 $cm = new ContentManager();
$banners = $cm->getInTime($banners);
//$advertisement->render($banners, &$tpl);
$advertisement->render($banners, $advertisement);


$intersticial = $advertisement->getIntersticial(650, '$category');
if (!empty($intersticial)) {
    $advertisement->render(array($intersticial), $advertisement);
}
 