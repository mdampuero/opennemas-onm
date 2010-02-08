<?php

//Cambiar por photo of day por ahora se muestra la primera.
require_once('core/pc_content_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_poll.class.php');

require_once('core/pollgraph.class.php');

$tpl->assign('MEDIA_CONECTA_WEB', MEDIA_CONECTA_WEB);

$pccm = new PC_ContentCategoryManager();
$allcategorys=$pccm->find_all_types('available=1');

$tpl->assign('allcategorys', $allcategorys);
$photos=$allcategorys[1];
$videos=$allcategorys[2];
 //Cogemos la primera categoria de fotos y de videos.
//Campos para index_columna3.tpl
$pc = new PC_ContentManager();
//$photodia = $pc->find('PC_Photo', 'content_status=1 and photo_of_day=1 and fk_pc_content_category=1', 'ORDER BY created DESC');

// Contents del Visor
$photodia = $pc->cache->find_by_category_name('PC_Photo', $photos[0]->name, 'favorite=1 and available=1 and content_status=0 ', 'ORDER BY changed DESC LIMIT 0, 1' ,'path_file, title');
$tpl->assign('photodia', $photodia[0]);
 
$videodia = $pc->cache->find_by_category_name('PC_Video', $videos[0]->name, 'favorite=1 and available=1 and content_status=0', 'ORDER BY changed DESC LIMIT 0, 1','code, title');
$tpl->assign('videodia', $videodia[0]);



//Datos para as graficas

$graficasconecta = $pc->find('PC_Poll', 'content_status=1 and view_column=1', 'ORDER BY changed DESC LIMIT 0,2');
$tpl->assign('graficasconecta', $graficasconecta);
//var_dump($graficasconecta);  //title(es la pregunta) total_votes

/*
$items_segunda=$poll->get_items($graficasconecta[1]->id );	
$tpl->assign('items_segunda', $items_segunda);
*/
//print_r($items_segunda); // item (reposta) votes (votos parciais)




