<?php

/**
 * Setup app
*/
require_once'../bootstrap.php';

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);

// Necesaria esta asignación para que funcione en index_sections.php e o menú
$category_name = $_GET['category_name'] = 'opinion';
$subcategory_name = null;
$section = $category_name;

$ccm = ContentCategoryManager::get_instance();
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
$tpl->assign('ccm', $ccm);

//Get rid of this as soon as posible
require_once 'sections.php';

$section = (!empty($subcategory_name))? $subcategory_name: $category_name;
$section = (is_null($section))? 'home': $section;
$tpl->assign('section', $section);

//Fetch opinions
$cm = new ContentManager();
$director  = $cm->find(
    'Opinion',
    'type_opinion=2 AND in_home=1 AND available=1 AND content_status=1',
    'ORDER BY created DESC  LIMIT 0,1'
);
$editorial = $cm->find(
    'Opinion',
    'type_opinion=1 AND in_home=1 AND available=1 AND content_status=1',
    'ORDER BY position ASC, created DESC LIMIT 0,2'
);
$opinions = $cm->getOpinionArticlesWithAuthorInfo(
    'available=1 AND type_opinion=0 AND content_status=1',
    'ORDER BY in_home DESC, position ASC, created DESC LIMIT 0,10'
);

$tpl->assign('editorial', $editorial);
if (isset ($director[0])) {
    $director[0]->name = 'Director';
    $tpl->assign('director', $director[0]);
}

//Obtener los slug's de los autores
foreach ($opinions as $i => $op) {
    $opinions[$i]['author_name_slug']=StringUtils::get_title($op['name']);
}

$tpl->assign('opinions', $opinions);

$tpl->display('mobile/opinion-index.tpl');

