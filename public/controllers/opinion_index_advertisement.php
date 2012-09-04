<?php
defined('SITE_URL') or die('Direct access is forbidden');

$ccm = ContentCategoryManager::get_instance();
$category_name = 'opinion';
$category = $ccm->get_id($category_name);
$category = (!isset($category) || ($category == 'home')) ? 0 : $category;

$advertisement = Advertisement::getInstance();
/* $banners = $advertisement->cache->getAdvertisements(array(1, 2, 3, 10, 12, 11, 13), $category); */
$banners = $advertisement->getAdvertisements(array(601, 602, 603, 605, 609, 610), $category);

$cm = new ContentManager();
$banners = $cm->getInTime($banners);
//$advertisement->renderMultiple($banners, &$tpl);
$advertisement->renderMultiple($banners, $advertisement);

//// Get intersticial banner
$intersticial = $advertisement->getIntersticial(650, $category);
if (!empty($intersticial)) {
    $advertisement->renderMultiple(array($intersticial), $advertisement);
}

