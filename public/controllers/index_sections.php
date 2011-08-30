<?php

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

//TODO: revisar (index.php repeat code )


$category_name = (isset($category_name)) ? $category_name : null;
$subcategory_name = (isset($subcategory_name)) ? $subcategory_name : null;
$category = $ccm->get_id($category_name);
$tpl->assign('category', $category);
$tpl->assign('category_name', $category_name);
$tpl->assign('category_real_name', $ccm->get_title($category_name));
$subcategory = $ccm->get_id($subcategory_name);
$tpl->assign('subcategory', $subcategory);
$tpl->assign('subcategory_name', $subcategory_name);
$tpl->assign('subcategory_real_name', $ccm->get_title($subcategory_name));

/*
// print in menu
$categories = array();
if (!empty($allcategorys)) {
    foreach ($allcategorys as $prima) {
        $subcat = $ccm->get_all_subcategories($prima->pk_content_category);
        $categories[$prima->posmenu] = array('id' => $prima->pk_content_category, 'name' => $prima->name, 'title' => $prima->title, 'internal_category' => $prima->internal_category, 'subcategories' => $subcat, 'posmenu' => $prima->posmenu, 'color' => $prima->color, 'logo' => $prima->logo_path);
        if (($category == $prima->pk_content_category) || ($subcategory == $prima->pk_content_category)) {
            $category_data = array('id' => $prima->pk_content_category, 'name' => $prima->name, 'title' => $prima->title, 'internal_category' => $prima->internal_category, 'subcategories' => $subcat, 'posmenu' => $prima->posmenu, 'color' => $prima->color, 'logo' => $prima->logo_path);
        }
    }
}

$tpl->assign('categories', $categories);
if (isset($categories_data)) {
    $tpl->assign('category_data', $category_data);
}

if (!is_null($category)) {
    $tpl->assign('posmenu', $ccm->get_pos($category));
}
*/

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

/* TODO: a mejorar
 * review vbles actual_category, subcategory.....
 */

$frontpages = array('video'=>'/videos.php/',
                    'album'=>'/album.php/',
                    'opinion'=>'/opinion_inner.php/',
                    'opinion'=>'/opinion_index.php/',
                    'frontpage'=>'/index.php/');

foreach($frontpages as $name=>$pattern) {

    if (preg_match($pattern, $_SERVER['SCRIPT_NAME'])) {
        $menuFrontpage= Menu::renderMenu($name);
    }
}

if (!isset($menuFrontpage) || empty($menuFrontpage->items)) {
    $menuFrontpage= Menu::renderMenu('frontpage');
}

$tpl->assign('menuFrontpage',$menuFrontpage->items);

if (empty($category_name) && !empty($menuFrontpage->items)) {
    foreach ($menuFrontpage->items as  $item){
        if(empty($category_name) && $item->type == 'category') {
             $category_name = $item->link;
             $category = $ccm->get_id($category_name);
        }
    }

}

if(empty($category_name)) {
    if (preg_match('/videos.php/', $_SERVER['SCRIPT_NAME'])) {
            $contentType = Content::getIDContentType('video');
    } elseif (preg_match('/album.php/', $_SERVER['SCRIPT_NAME'])) {
            $contentType = Content::getIDContentType('album');
    } else {
        $contentType = 1;
    }

    //get first category
    list($allcategorys, $subcat, $categoryData) = $ccm->getArraysMenu(0, $contentType);
    $category_name = $categoryData[0]->name;
    $category = $categoryData[0]->pk_content_category;
    $category_title = $categoryData[0]->title;

}

$actual_category = $category_name;

$tpl->assign(array( 'category_name'=>$category_name ,
    'category'=>$category ,
    'actual_category'=>$actual_category ,
    'category_real_name', $ccm->get_title($category_name)
    ) );


$actual_category_id=$ccm->get_id($actual_category);
$tpl->assign('actual_category_id',$actual_category_id);

