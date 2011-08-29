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

if (preg_match('/videos\.php/', $_SERVER['SCRIPT_NAME'])) {

    $allcategorys = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 ' .
									  'AND (internal_category = 1 OR internal_category = 5)',
									  'ORDER BY posmenu');
	
} elseif (preg_match('/gallery\.php/', $_SERVER['SCRIPT_NAME'])) {
	
    $allcategorys = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 '.
									  'AND (internal_category = 1 '.
									  'OR internal_category = 3)',
									  'ORDER BY posmenu');
	
} else {
	 
    $allcategorys = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 '.
									  'AND (internal_category < 3 )',

									  'ORDER BY posmenu');
	
}

// print in menu
$categories = array();
foreach ($allcategorys as $prima) {
    $subcat = $ccm->get_all_subcategories($prima->pk_content_category);
    $categories[$prima->posmenu] = array('id' => $prima->pk_content_category, 'name' => $prima->name, 'title' => $prima->title, 'internal_category' => $prima->internal_category, 'subcategories' => $subcat, 'posmenu' => $prima->posmenu, 'color' => $prima->color, 'logo' => $prima->logo_path);
    if (($category == $prima->pk_content_category) || ($subcategory == $prima->pk_content_category)) {
        $category_data = array('id' => $prima->pk_content_category, 'name' => $prima->name, 'title' => $prima->title, 'internal_category' => $prima->internal_category, 'subcategories' => $subcat, 'posmenu' => $prima->posmenu, 'color' => $prima->color, 'logo' => $prima->logo_path);
    }
}

$tpl->assign('categories', $categories);
if (isset($categories_data)) {
    $tpl->assign('category_data', $category_data);
}

if (!is_null($category)) {
    $tpl->assign('posmenu', $ccm->get_pos($category));
}


// Styles to print each category's new
$styles = '';
foreach ($allcategorys as $the_category) {
    if (empty($the_category->color)) {
        $the_category->color = '#638F38';
    }
    $styles.= "\tdiv.nw-big div." . $the_category->name . ", div.nw-big-img-lateral" . " div." . $the_category->name . "{ color:" . $the_category->color . "; }\n\t\t" . "div.nw-big div." . $the_category->name . " span, " . "div.nw-big-img-lateral div." . $the_category->name . " " . "span{ background-color:" . $the_category->color . ";}\n";
}

if (empty($category_data['color'])) {
    $category_data['color'] = '#638F38';
}

$styles.= "\tdiv#submenu > ul, div.toolbar-bottom a, div.utilities a," . " .transparent-logo{ background-color:" . $category_data['color'] . " !important; }\n";
$tpl->assign('categories_styles', $styles);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

/* TODO: a mejorar*/

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