<?php
defined('SITE_URL') or die('Direct access is forbidden');

$advertisement = Advertisement::getInstance();

// APC cache version
/* $banners = $advertisement->cache->getAdvertisements(array(1,2,3, 5, 10,12, 11,13), $category); */
$banners = $advertisement->getAdvertisements(array(1, 2, 103, 105, 9, 10));
$cm = new ContentManager();
$banners = $cm->getInTime($banners);
//$advertisement->render($banners, &$tpl);
$advertisement->render($banners, $advertisement);
