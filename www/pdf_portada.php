<?php
// Get PDFs of frontpage from newspaper and magazines
//kiosko-xornal  -> Frontpage
//kiosko-exit -> Exit
//kiosko-nos -> Nos
//kiosko-contexto -> Contexto
//kiosko-estratexias -> Estratexias

$ccm = new ContentCategoryManager();
$allcategorys = $ccm->find('internal_category=4  AND fk_content_category=0', 'ORDER BY posmenu');

$cm = new ContentManager();
foreach ($allcategorys as $category) {
    $kiosko[$category->name] =  $cm->find_by_category('Kiosko', $category->pk_content_category,
                                  ' `contents`.`available`=1 AND `contents`.`fk_content_type`=14 AND `kioskos`.`favorite`=1');
}

$tpl->assign('frontpage_newspaper_img', $kiosko['kiosko-xornal'][0]->path.preg_replace("/.pdf$/",".jpg",$kiosko['kiosko-xornal'][0]->name));
$tpl->assign('frontpage_newspaper_pdf', $kiosko['kiosko-xornal'][0]->path.$kiosko['kiosko-xornal'][0]->name);

$tpl->assign('frontpage_exit_img', $kiosko['kiosko-exit'][0]->path.preg_replace("/.pdf$/",".jpg",$kiosko['kiosko-exit'][0]->name));
$tpl->assign('frontpage_exit_pdf', $kiosko['kiosko-exit'][0]->path.$kiosko['kiosko-exit'][0]->name);

//
$tpl->assign('frontpage_nos_img', $kiosko['kiosko-nos'][0]->path.preg_replace("/.pdf$/",".jpg",$kiosko['kiosko-nos'][0]->name));
$tpl->assign('frontpage_nos_pdf', $kiosko['kiosko-nos'][0]->path.$kiosko['kiosko-nos'][0]->name);

//
$tpl->assign('frontpage_contexto_img', $kiosko['kiosko-contexto'][0]->path.preg_replace("/.pdf$/",".jpg",$kiosko['kiosko-contexto'][0]->name));
$tpl->assign('frontpage_contexto_pdf', $kiosko['kiosko-contexto'][0]->path.$kiosko['kiosko-contexto'][0]->name);

//
$tpl->assign('frontpage_estratexias_img', $kiosko['kiosko-estratexias'][0]->path.preg_replace("/.pdf$/",".jpg",$kiosko['kiosko-estratexias'][0]->name));
$tpl->assign('frontpage_estratexias_pdf', $kiosko['kiosko-estratexias'][0]->path.$kiosko['kiosko-estratexias'][0]->name);
