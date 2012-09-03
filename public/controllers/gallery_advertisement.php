<?php
defined('SITE_URL') or die('Direct access is forbidden');

$ccm = ContentCategoryManager::get_instance();
$category_name='album';
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

// Load internal banners, principal banners (1,2,3,11,13) and use cache to performance
/* $banners = $advertisement->cache->getAdvertisements(array(1, 2, 3, 10, 12, 11, 13), $category); */
$banners = $advertisement->getAdvertisements(array(401, 402, 403, 405, 409, 410), $category);
 $cm = new ContentManager();
$banners = $cm->getInTime($banners);
//$advertisement->renderMultiple($banners, &$tpl);
$advertisement->renderMultiple($banners, $advertisement);

$intersticial = $advertisement->getIntersticial(50, '$category');
if (!empty($intersticial)) {
    $advertisement->renderMultiple(array($intersticial), $advertisement);
}
