<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

$tpl = new Template(TEMPLATE_USER);

//category está inicializada?
//siempre debe xistir la categoría por defecto
//con pk = 1


//Obtenemos los articulos de esa fecha seg�n la categoria
$cm = new ContentManager();

if (!isset($_GET['category'])) $_GET['category'] = 1;

$tpl->assign('category', $_GET['category']);


$cc = new ContentCategoryManager();
	

			// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
				$allcategorys = $cc->find('inmenu=1 AND fk_content_category=0', 'ORDER BY posmenu');	
			$i=0;
			foreach( $allcategorys as $prima) {				
				$subcat[$i]=$cc->find(' inmenu=1 AND fk_content_category ='.$prima->pk_content_category, 'ORDER BY posmenu');					
				 $i++;
			}

			$tpl->assign('subcat', $subcat);				
			// FIXME: Set pagination
			$tpl->assign('allcategorys', $allcategorys);
			$datos_cat = $cc->find('pk_content_category='.$_GET['category'], NULL);	
			$tpl->assign('datos_cat', $datos_cat);
		

/******FECHAS Y CALENDARIO HEMEROTECA*******/
if (!isset($_GET['semmes'])) $_GET['semmes'] = number_format((($hoy-$tstamp)/(60*60*24*7)),0);

$tpl->assign('semmes', $_GET['semmes']);


$hoy=mktime(0,0,0,date(m),date(d),date(Y));
$tstamp = $_GET['efecha'];
if(empty($_GET['efecha'])) {
	$tstamp=$hoy; $_GET['efecha']=$hoy;}
else{
	$tstamp = $_GET['efecha'];
	$month = date('n',$tstamp);
    $year = date('Y',$tstamp);

}

$data = date("d-m-Y",$tstamp);

$tpl->assign('data', $data);

$tpl->assign('efecha', $_GET['efecha']);
$tpl->assign('month', $month);
$tpl->assign('year', $year);
//***********************************************+*******************

$artviewed = $cm->find_by_category('Article',$_GET['category'], 'content_status=1 AND frontpage=1 AND fk_content_type=1', 'ORDER BY views DESC');
$tpl->assign('artviewed', $artviewed);


//ContentManager::find_by_category(<TIPO_CONTENIDO>, <CATEGORY>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
//$noticias = $cm->find_by_category('Article',$_GET['category'], 'DATE(`created`) ="'.$fecha.'" ', 'ORDER BY created DESC');
//$noticias = $cm->find('Article',  'DATE(`created`) ="'.$fecha.'" ' , 'ORDER BY created DESC');

$noticias = $cm->find_by_category('Article',$_GET['category'], '(content_status <> 3) AND (archive ="'.$_GET['semmes'].'") AND fk_content_type=1', 'ORDER BY position ASC');
$tpl->assign('noticias', $noticias);


//Noticias colum derecha
$articles2 = $cm->find('Article', ' content_status=1 ', 'ORDER BY views DESC,created DESC LIMIT 0,10');
$tpl->assign('articles', $articles2);

  
/*********************************PUBLICIDAD***********************************/
// type_advertisement: 1- Banner1, 2- Banner2, 3- Banner3, 4- B.Intermedio
// 5- Lateral, 6-Boton, 7-RobaP�gina
//fk_content_categories->si se quiere por categorias 1-9 secciones 0 generica

$banner1 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=1 AND (fk_content_categories='.$_GET['category'].')', 'ORDER BY created DESC');
$banner2 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=2 AND (fk_content_categories='.$_GET['category'].')', 'ORDER BY created DESC');
$banner3 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=3 AND (fk_content_categories='.$_GET['category'].' )', 'ORDER BY created DESC');
$binter = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=4 AND (fk_content_categories='.$_GET['category'].' )', 'ORDER BY created DESC');
$buton = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=5 AND (fk_content_categories='.$_GET['category'].' )', 'ORDER BY created DESC');
$lateral = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=6 AND (fk_content_categories='.$_GET['category'].' )', 'ORDER BY created DESC');

$result = count($banner1);
if($result==0){
	$banner1 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=1 AND (fk_content_categories=0)', 'ORDER BY created DESC');
	$result = count($banner1);
}
$r=rand(0,--$result);
$tpl->assign('banner1', $banner1[$r]);
$adv=$banner1[$r]->img;				
if(isset($adv)){	
	$esta   = strripos($adv, '.SWF');
     $tpl->assign('isSWFb1',$esta);	 
}

$result = count($banner2);
if($result==0){
	$banner2 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=2 AND (fk_content_categories=0)', 'ORDER BY created DESC');
	$result = count($banner2);
}
$r=rand(0,--$result);
$tpl->assign('banner2', $banner2[$r]);
$adv=$banner2[$r]->img;				
if(isset($adv)){	
	$esta   = strripos($adv, '.SWF');
     $tpl->assign('isSWFb2',$esta);	 
}

$result = count($banner3);
if($result==0){
	$banner3 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=3 AND (fk_content_categories=0)', 'ORDER BY created DESC');
	$result = count($banner3);
}
$r=rand(0,--$result);
$tpl->assign('banner3', $banner3[$r]);
$adv=$banner3[$r]->img;				
if(isset($adv)){	
	$esta   = strripos($adv, '.SWF');
     $tpl->assign('isSWFb3',$esta);	 
}

$result = count($binter);
if($result==0){
	$binter = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=4 AND (fk_content_categories=0)', 'ORDER BY created DESC');
	$result = count($binter);
}
$r=rand(0,--$result);
$tpl->assign('binter', $binter[$r]);
$adv=$binter[$r]->img;				
if(isset($adv)){	
	$esta   = strripos($adv, '.SWF');
     $tpl->assign('isSWFbi',$esta);	 
}

$result = count($lateral);
if($result==0){
	$lateral = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=6 AND (fk_content_categories=0)', 'ORDER BY created DESC');
	$result = count($lateral);
}
$r=rand(0,--$result);
$tpl->assign('lateral', $lateral[$r]);
$adv=$lateral[$r]->img;				
if(isset($adv)){	
	$esta   = strripos($adv, '.SWF');
     $tpl->assign('isSWFla',$esta);	 
}

$result = count($buton);
if($result==0){
	$buton = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=5 AND (fk_content_categories=0)', 'ORDER BY created DESC');
	$result = count($buton);
}
$r=rand(0,--$result);
$tpl->assign('boton1', $buton[$r]);
$adv=$buton[$r]->img;				
if(isset($adv)){	
	$esta   = strripos($adv, '.SWF');
     $tpl->assign('isSWFbu1',$esta);	 
}

if(($result==0) or ($result==1)){
	$buton = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=5 AND (fk_content_categories=0)', 'ORDER BY created DESC');
	$result = count($buton);
}
$r=rand(0,--$result);
$tpl->assign('boton2', $buton[$r]);
$adv=$buton[$r]->img;				
if(isset($adv)){	
	$esta   = strripos($adv, '.SWF');
     $tpl->assign('isSWFbu2',$esta);	 
}


// Visualizar
$tpl->display('hemeroteca.tpl');