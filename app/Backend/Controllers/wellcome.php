<?php
/**
 * Setup app
*/
require_once '../bootstrap.php';
require_once './session_bootstrap.php';

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

//Para crear noticia que vuelva a listado de pendientes.
$_SESSION['desde']='index_portada';

$feeds = array (
    array('name' => 'El pais', 'url'=> 'http://www.elpais.com/rss/feed.html?feedId=1022'),
    array('name' => '20 minutos', 'url'=> 'http://20minutos.feedsportal.com/c/32489/f/478284/index.rss'),
    array('name' => 'Publico.es', 'url'=> 'http://www.publico.es/rss/'),
    array('name' => 'El mundo', 'url'=> 'http://elmundo.feedsportal.com/elmundo/rss/portada.xml'),
);

$tpl->assign('feeds',$feeds);
$tpl->display('welcome/index.tpl');
