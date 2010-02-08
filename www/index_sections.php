<?php
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$category = $ccm->get_id($category_name);
$tpl->assign('category', $category);
$tpl->assign('category_name', $category_name);

$tpl->assign('category_real_name', $ccm->get_title($category_name));

$subcategory = $ccm->get_id($subcategory_name);
$tpl->assign('subcategory', $subcategory);
$tpl->assign('subcategory_name', $subcategory_name);
$tpl->assign('subcategory_real_name', $ccm->get_title($subcategory_name));

$allcategorys = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 ', 'ORDER BY posmenu');

$categories = array();
foreach( $allcategorys as $prima) {
    $subcat = $ccm->get_all_subcategories( $prima->pk_content_category );
    
    $categories[ $prima->posmenu ] = array( 'name' => $prima->name,
                                            'title' => $prima->title,
                                            'subcategories' => $subcat);
}

$tpl->assign('categories', $categories);

if( !is_null($category) ) {
    $tpl->assign('posmenu', $ccm->get_pos($category));
}
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

