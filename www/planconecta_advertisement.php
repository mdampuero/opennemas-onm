<?php
defined('SITE_URL') or die('Direct access is forbidden');

$ccm = ContentCategoryManager::get_instance();
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

/* $banners = $advertisement->cache->getAdvertisements(array(1,2,3, 10,12, 11,13), $category); */
$banners = $advertisement->getAdvertisements(array(1,2,3, 10,12, 11,13), $category);

//$advertisement->render($banners, &$tpl);
$advertisement->render($banners, &$advertisement);