<?php
//Provisional mientras no haya en las categorias
$actual_category_id = 0;
//$last_gallerys = $cm->find_by_category('Album', $actual_category_id, ' contents.fk_content_type=7 and contents.available=1', 'ORDER BY created DESC LIMIT 0 , 5');
$num_max = 5;
$last_gallerys = $cm->find('Album', ' contents.fk_content_type=7 and contents.available=1', 'ORDER BY created DESC LIMIT 0 , ' . $num_max);
$gallerys_viewed = $cm->cache->getMostViewedContent('Album', true, $actual_category_id, 0, 30, $num_max);
$gallerys_voted = $cm->cache->getMostVotedContent('Album', true, $actual_category_id, 0, 30, $num_max);
$most_comments = $cm->cache->getMostComentedContent('Album', true, $actual_category_id, 0, 30, $num_max);
$tpl->assign('last_gallerys', $last_gallerys);
$tpl->assign('gallerys_viewed', $gallerys_viewed);
$tpl->assign('gallerys_voted', $gallerys_voted);
if (!empty($most_comments) && count($most_comments) > 0) {
    $gallerys_comments = array();
    foreach ($most_comments as $ar_gallery) {
        $album = new Album($ar_gallery['pk_content']);
        $gallerys_comments[] = $album;
    }
    $tpl->assign('gallerys_comments', $gallerys_comments);
}
