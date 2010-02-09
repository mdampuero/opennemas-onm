<?php
require('../config.inc.php');
require_once('../core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('../core/content_manager.class.php');
require_once('../core/content.class.php');
require_once('../core/opinion.class.php');

require('../core/photo.class.php');
require('../core/author.class.php');
require('../core/content_category.class.php');
require('../core/content_category_manager.class.php');

$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$ccm = new ContentCategoryManager();

// Necesaria esta asignación para que funcione en index_sections.php e o menú
$category_name = $_GET['category_name'] = 'opinion';
$subcategory_name = null;

$ccm = ContentCategoryManager::get_instance();
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);

$tpl->assign('ccm', $ccm);

$section = (!empty($subcategory_name))? $subcategory_name: $category_name;
$section = (is_null($section))? 'home': $section;
$tpl->assign('section', $section);

//Listado de opiniones portada seccion opinion.           
$cm = new ContentManager();

$editorial = $cm->find('Opinion', 'type_opinion=1 AND in_home=1 AND available=1 AND content_status=1', 'ORDER BY position ASC, created DESC LIMIT 0,2');
$director  = $cm->find('Opinion', 'type_opinion=2 AND in_home=1 AND available=1 AND content_status=1', 'ORDER BY created DESC  LIMIT 0,1');
$aut = new Author($director[0]->fk_author);                        

//  Director
$foto = $aut->get_photo($director[0]->fk_author_img);
$dir['photo'] = $foto->path_img;
$dir['name']  =	$aut->name;
$tpl->assign('dir', $dir); // Director

// /media/images/authors/jose-luis-gomez/2009022801490250024.gif
$opinions = $cm->find_listAuthors('available=1 AND type_opinion=0 AND content_status=1', 'ORDER BY in_home DESC, position ASC, created DESC LIMIT 0,10');
$tpl->assign('opinions', $opinions);

//var_dump($editorial);
//var_dump($director[0]);
//die();

$tpl->assign('editorial', $editorial);
$tpl->assign('director', $director[0]);		

$aut = new Author();
$todos = $aut->all_authors(NULL,'ORDER BY name');
$tpl->assign('autores', $todos); //combobox1
$tpl->assign('todos_pag', $todos); //combobox2


// Visualizar
$tpl->display('mobile/opinion_list.tpl');
