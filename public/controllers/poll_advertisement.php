<?php
defined('SITE_URL') or die('Direct access is forbidden');

$ccm = ContentCategoryManager::get_instance();
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

// Load internal banners, principal banners (1,2,3,11,13) and use cache to performance
/* $banners = $advertisement->cache->getAdvertisements(array(1, 2, 3, 10, 12, 11, 13), $category); */
$banners = $advertisement->getAdvertisements(array(801, 802, 803, 805, 809, 810), $category);
 $cm = new ContentManager();
$banners = $cm->getInTime($banners);
//$advertisement->renderMultiple($banners, &$tpl);
$advertisement->renderMultiple($banners, $advertisement);

$intersticial = $advertisement->getIntersticial(850, '$category');
if (!empty($intersticial)) {
    $advertisement->renderMultiple(array($intersticial), $advertisement);
}
