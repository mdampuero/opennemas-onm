<?php
//error_reporting(E_ALL);
require_once('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');
require_once('core/photo.class.php');

require_once('core/advertisement.class.php');

$tpl = new Template(TEMPLATE_USER);

/******************** CATEGORIA ********************************************************/
$ccm = new ContentCategoryManager();
$cm = new ContentManager();

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


// Base de datos coas localidades, parámetros para a petición a aemet y nome da cache
$localidades = array(
    'pontevedra' => array(
        'querystring' => 'p=36&l=36001',
        'titulo'      => 'Pontevedra',
        'cachename'   => 'aemet.pontevedra',
    ),
    'vigo'       => array(
        'querystring' => 'p=36&l=36560',
        'titulo'      => 'Vigo',
        'cachename'   => 'aemet.vigo',
    ),
    'acoruna'    => array(
        'querystring' => 'p=15&l=15001',
        'titulo'      => 'A Coruña',
        'cachename'   => 'aemet.acoruna',
    ),
    'ferrol'     => array(
        'querystring' => 'p=15&l=15350',
        'titulo'      => 'Ferrol',
        'cachename'   => 'aemet.ferrol',
    ),
    'santiago'   => array(
        'querystring' => 'p=15&l=15770',
        'titulo'      => 'Santiago de Compostela',
        'cachename'   => 'aemet.santiago',
    ),
    'ourense'    => array(
        'querystring' => 'p=32&l=32001',
        'titulo'      => 'Ourense',
        'cachename'   => 'aemet.ourense',
    ),
    'lugo'       => array(
        'querystring' => 'p=27&l=27001',
        'titulo'      => 'Lugo',
        'cachename'   => 'aemet.lugo',
    ),
);

// FIXME: protexer variable con filter
$l = $_GET[ 'l' ];

if( in_array($l, array_keys($localidades)) ) {
    
    $tpl->assign('title', 'El tiempo en '.$localidades[ $l ]['titulo'] );
    $tpl->assign('titulo', $localidades[ $l ]['titulo']);    
    
    $tpl->assign('localidade', $l);
    $tpl->assign('querystring', $localidades[ $l ]['querystring']);
    $tpl->assign('cachename', $localidades[ $l ]['cachename']);
} else {
    
    $tpl->assign('title', 'El tiempo');
    $tpl->assign('localidade', NULL);
}

require_once("weather_advertisement.php");

// FIXME: non facer esto, empregar na plantilla {$smarty.const.MEDIA_IMG_PATH_WEB}
$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);

// Visualizar
$tpl->display('weather.tpl');
