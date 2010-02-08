<?php

//Cambiar por photo of day por ahora se muestra la primera.
 
require_once('core/pc_content_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
 
require_once('core/pollgraph.class.php');

$tpl->assign('MEDIA_CONECTA_WEB', MEDIA_CONECTA_WEB);

//Campos para el visor del modulo_columna3_containerFotoVideoDiaMasListado.tpl

//Campos para index_columna3.tpl
$pc = new PC_ContentManager();
//$photodia = $pc->find('PC_Photo', 'content_status=1 and photo_of_day=1 and fk_pc_content_category=1', 'ORDER BY created DESC');

// Contents del Visor
$photodia = $pc->cache->find_by_category_name('PC_Photo', 'foto-dia', 'favorite=1 and content_status=1', 'ORDER BY changed DESC LIMIT 0, 1' ,'path_file, title');
$tpl->assign('photodia', $photodia[0]);

$videodia = $pc->cache->find_by_category_name('PC_Video', 'video-dia', 'favorite=1 and content_status=1', 'ORDER BY changed DESC LIMIT 0, 1','code, title');
$tpl->assign('videodia', $videodia[0]);

