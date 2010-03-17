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

if( preg_match('/video\.php/',$_SERVER['SCRIPT_NAME'])){
    $allcategorys = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 AND (internal_category =1 OR internal_category = 5)', 'ORDER BY internal_category DESC, posmenu');
}else{
    $allcategorys = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 AND (internal_category < 3 )', 'ORDER BY posmenu');
}
$categories = array();

foreach( $allcategorys as $prima) {
    $subcat = $ccm->get_all_subcategories( $prima->pk_content_category );
    
    $categories[ $prima->posmenu ] = array( 'name' => $prima->name,
                                            'title' => $prima->title,
                                            'subcategories' => $subcat,
                                            'color' => $prima->color,
                                            'logo' => 'media/sections/'.$prima->logo_path);
}
 
$tpl->assign('categories', $categories);

if( !is_null($category) ) {
    $tpl->assign('posmenu', $ccm->get_pos($category));
}
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

