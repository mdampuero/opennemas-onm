<?php
defined('SITE_URL') or die('Direct access is forbidden');

$ccm = ContentCategoryManager::get_instance();
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

$banners = $advertisement->getAdvertisements(array(101, 102, 103, 104, 105, 106, 107), $category);

//$advertisement->render($banners, &$tpl);
$advertisement->render($banners, &$advertisement);

// Get intersticial banner
$intersticial = $advertisement->getIntersticial(150, $category);
$advertisement->render(array($intersticial), &$advertisement);