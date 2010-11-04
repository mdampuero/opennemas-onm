<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Set up view
*/
$tpl = new Template(TEMPLATE_USER);

/**
 * Set up content managers
*/
$ccm = new ContentCategoryManager();
$cm = new ContentManager();

require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
// BBDD with the poblations, request parameters for AEMET and cache name
$localidades = array('pontevedra' => array('querystring' => 'p=36&l=36001',
                                           'titulo' => 'Pontevedra',
                                           'cachename' => 'aemet.pontevedra',),
                     'vigo' => array('querystring' => 'p=36&l=36560',
                                     'titulo' => 'Vigo',
                                     'cachename' => 'aemet.vigo',),
                     'acoruna' => array('querystring' => 'p=15&l=15001',
                                        'titulo' => 'A Coruña',
                                        'cachename' => 'aemet.acoruna',),
                     'ferrol' => array('querystring' => 'p=15&l=15350',
                                       'titulo' => 'Ferrol',
                                       'cachename' => 'aemet.ferrol',),
                     'santiago' => array('querystring' => 'p=15&l=15770',
                                         'titulo' => 'Santiago de Compostela',
                                         'cachename' => 'aemet.santiago',),
                     'ourense' => array('querystring' => 'p=32&l=32001',
                                        'titulo' => 'Ourense',
                                        'cachename' => 'aemet.ourense',),
                     'lugo' => array('querystring' => 'p=27&l=27001',
                                     'titulo' => 'Lugo',
                                     'cachename' => 'aemet.lugo',),
                     );

$l = filter_input(INPUT_GET,'l',FILTER_SANITIZE_STRING);
if (in_array($l, array_keys($localidades))) {
    $tpl->assign('title', 'El tiempo en ' . $localidades[$l]['titulo']);
    $tpl->assign('titulo', $localidades[$l]['titulo']);
    $tpl->assign('localidade', $l);
    $tpl->assign('querystring', $localidades[$l]['querystring']);
    $tpl->assign('cachename', $localidades[$l]['cachename']);
} else {
    $tpl->assign('title', 'El tiempo');
    $tpl->assign('localidade', NULL);
}
require_once ("weather_advertisement.php");
// Render
$tpl->display('weather.tpl');
