<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$ccm = new ContentCategoryManager();

require('sections.php');

// Necesaria esta asignación para que funcione en index_sections.php e o menú
$category_name = $_GET['category_name'] = 'opinion';
$subcategory_name = null;
$section = $category_name;

$ccm = ContentCategoryManager::get_instance();
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);

$tpl->assign('ccm', $ccm);

$section = (!empty($subcategory_name))? $subcategory_name: $category_name;
$section = (is_null($section))? 'home': $section;
$tpl->assign('section', $section);

//Listado de opiniones portada seccion opinion.           
$cm = new ContentManager();
$director  = $cm->find('Opinion', 'type_opinion=2 AND in_home=1 AND available=1 AND content_status=1', 'ORDER BY created DESC  LIMIT 0,1');
$editorial = $cm->find('Opinion', 'type_opinion=1 AND in_home=1 AND available=1 AND content_status=1', 'ORDER BY position ASC, created DESC LIMIT 0,2');
$opinions = $cm->find_listAuthors('available=1 AND type_opinion=0 AND content_status=1', 'ORDER BY in_home DESC, position ASC, created DESC LIMIT 0,10');

//Comprobación de index undefined php5.3
$tpl->assign('editorial', $editorial);
if(isset ($director[0])){
    $director[0]->name = 'Director';
    $tpl->assign('director', $director[0]);
}

//Obtener los slug's de los autores
$i=0;
foreach ($opinions as $op){
    $opinions[$i]['author_name_slug']=String_Utils::get_title($op['name']);
    $i++;
}

$tpl->assign('opinions', $opinions);

//$aut = new Author();
//$todos = $aut->all_authors(NULL,'ORDER BY name');
//$tpl->assign('autores', $todos); //combobox1
//$tpl->assign('todos_pag', $todos); //combobox2


// Visualizar
$tpl->display('mobile/opinion-index.tpl');
