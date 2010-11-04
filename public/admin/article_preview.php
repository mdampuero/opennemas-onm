<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

require(SITE_CORE_PATH.'media.manager.class.php');

//category está inicializada?
//siempre debe xistir la categoría por defecto
//con pk = 1

if (!isset($_GET['semmes'])) $_GET['semmes'] = 1;
if (!isset($_GET['desde'])) $_GET['desde'] = 0;
$tpl->assign('desde', $_GET['desde']);

$cc = new ContentCategoryManager();
$cm = new ContentManager();

//Is category initialized
if (!isset($_GET['category_name'])) { //Application::forward('/home/');

//Instruction for ending the php script
//OR redirect the user to 404.html
}
else {
    $category = $cc->get_id($_GET['category_name']);
    $tpl->assign('category', $category);
    $tpl->assign('category_name', $_GET['category_name']);

    if (isset($_GET['subcategory_name']))
	{$subcategory = $cc->get_id($_GET['subcategory_name']);$tpl->assign('subcategory', $subcategory);}
}

//**********************************//
//Dates information
$hoy=mktime(0,0,0,date(m),date(d),date(Y));

if(empty($_GET['efecha'])) { $tstamp=$hoy; $_GET['efecha']=$hoy;}
else{
    $tstamp = $_GET['efecha'];
    $month = date('n',$tstamp);
    $year = date('Y',$tstamp);
  }
$tpl->assign('efecha', $_GET['efecha']);
if (!isset($_GET['semmes'])) $_GET['semmes'] = number_format((($hoy-$tstamp)/(60*60*24*7)),0);
$tpl->assign('semmes', $_GET['semmes']);
$tpl->assign('month', $month);
$tpl->assign('year', $year);
//Dates information
//**********************************//

$article = new Article($_REQUEST['article_id']);
if($article->available==1){
    $article->setNumViews($_GET['article_id']);
    $tpl->assign('article', $article);

    if(isset($article->img2) and ($article->img2 != 0)){
        $photo2=new Photo($article->img2);

    }
    $tpl->assign('laphoto2', $photo2);
}

$tpl->assign('show', true);

$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);

// Visualizar
$tpl->display('article_preview.tpl');

?>
