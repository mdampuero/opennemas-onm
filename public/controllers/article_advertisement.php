<?php
//Provisional misma publicidad top y buttom en todo

defined('SITE_URL') or die('Direct access is forbidden');

$ccm = ContentCategoryManager::get_instance();
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category_name=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

$positions = array(101, 102, 103, 104, 105, 109, 110);

$banners = $advertisement->getAdvertisements($positions, $category);

if(count($banners<=0)){
 $cm = new ContentManager();
$banners = $cm->getInTime($banners);
    //$advertisement->render($banners, &$tpl);
    $advertisement->render($banners, $advertisement);
}
// Get intersticial banner,1,2,9,10
$intersticial = $advertisement->getIntersticial(150, $category);
if (!empty($intersticial)) {
    $advertisement->render(array($intersticial), $advertisement);
}