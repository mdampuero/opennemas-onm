<?php
defined('SITE_URL') or die('Direct access is forbidden');
/*
    1.- big superior izq
    2.- big superior derecha
    3.- banner cabecera
    4.- banner flotante derecho
    5.- 1er botoncuadrado columna
    6.- lateral derecho
    7.- separador horizontal
    8.- mini1 dcha
    9.- mini 2 dcha
    10.- big banner inferior1
    11.- boton dcha inferior1
    12.- big banner inferior2
    13.- anuncios google
    14.- 2o botoncuadrado columna2
    15.- 2o lateral derecho
    16.- 3er botoncuadrado columna2 
*/

$ccm = ContentCategoryManager::get_instance();
$category = $ccm->get_id($category_name);

$category = (!isset($category) || ($category=='home'))? 0: $category;
$advertisement = Advertisement::getInstance();

// Load 1-16 banners and use cache to performance
//$banners = $advertisement->getAdvertisements(range(1, 16), $category); // 4,9 unused
$banners = $advertisement->getAdvertisements(array(1,2, 3,4, 5,6,7, 8, 9,10), $category);
 $cm = new ContentManager();
$banners = $cm->getInTime($banners);
//$advertisement->render($banners, &$tpl);
$advertisement->render($banners, $advertisement);

// Get intersticial banner
$intersticial = $advertisement->getIntersticial(50, $category);
$advertisement->render(array($intersticial), $advertisement);
