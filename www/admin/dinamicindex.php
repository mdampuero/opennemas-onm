<?php
//error_reporting(E_ALL);

header("Content-Type: text/html; charset=UTF-8");
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/article.class.php');
require_once('core/advertisement.class.php');
require_once('core/related_content.class.php');
require_once('core/attachment.class.php');
require_once('core/attach_content.class.php');

require_once('core/opinion.class.php');


require('core/media.manager.class.php');
require_once('core/img_galery.class.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

//category está inicializada?
//siempre debe xistir la categoría por defecto
//con pk = 1
if (!isset($_GET['category'])) $_GET['category'] = 1;
if (!isset($_GET['semmes'])) $_GET['semmes'] = 1;
$tpl->assign('category', $_GET['category']);

//Obtenemos los artículos
$cm = new ContentManager();


	//content_status= 0 no publicado, 1 publicado 3 pendiente
	// ContentManager::find_by_category(<TIPO_CONTENIDO>, <CATEGORY>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
	$articles = $cm->find_by_category('Article',$_GET['category'], ' content_status=1 AND frontpage=1 AND fk_content_type=1', 'ORDER BY position ASC, created DESC');
	
	$all_frontpage_articles = $cm->find_by_category('Article',$_GET['category'], ' content_status=1 AND frontpage=1', 'ORDER BY position ASC, created DESC');
	
	$artviewed = $cm->find_by_category('Article',$_GET['category'], 'content_status=1 AND frontpage=1 AND fk_content_type=1', 'ORDER BY views DESC');
	
	$destacado = $articles[0];
	
	$oddpublished = $cm->find_by_category('Article',$_GET['category'], 'content_status=1 AND frontpage=1 AND position>=2 AND position%2=1 AND fk_content_type=1', 'ORDER BY position ASC, archive DESC LIMIT 0,24');
	$evenpublished = $cm->find_by_category('Article',$_GET['category'], 'content_status=1 AND frontpage=1 AND position>=2 AND position%2=0 AND fk_content_type=1', 'ORDER BY position ASC, archive DESC LIMIT 0,24');

	$tpl->assign('oddpublished', $oddpublished);
	$tpl->assign('evenpublished', $evenpublished);
	$tpl->assign('all_frontpage_articles', $all_frontpage_articles);
	$tpl->assign('artviewed', $artviewed);
	$tpl->assign('destacado', $destacado);

	$ArtPerColumn = 12;
	
	$column1 = array();
	$relia= new Related_content();
	$cm2 = new ContentManager();
	$relationed=array();
	$relat_c1=array();
 
for ( $c = 0,$aux = 0; $c <= $ArtPerColumn,$evenpublished[$aux]->title != "" ; $c++, $aux ++ ) {
	$column1[$c] = $evenpublished[$aux];
		
	//related articles para column1
	$relationes = $relia->get_relations($evenpublished[$aux]->id); //se le pasa el id de cada noticia de la column1
	// devueve array con los id de las noticias relacionadas
	$i=0;	
	foreach($relationes as $id_rel)
	{
		//Se recorre el array para con el id localizar la tabla con todos los campos de la noticia relacionada
		$relationed[$i]=$cm2->find('Article' , 'pk_content="'.$id_rel.'" ' , 'ORDER BY position DESC');

		//se genera un array para cada noticia se incluye todas las noticias relacionadas 
		$relat_c1[$c][]=$relationed[$i][0];
		$i++;
	 }
	 //se pasa un array multiple con los id de la noticia de la colum1 y  las relacionadas con esta
	 $tpl->assign('relationed_c1', $relat_c1);

      $attach_rel = new Attach_content();
	        $reles=array();
			$reles=$attach_rel->get_attach_relations($evenpublished[$aux]->id);
			
			  foreach($reles as $attaches) {				 
				     $resul = new Attachment($attaches);	      			
				     $adjuntos1[$c][]=$resul;	
				     $cat1[$c][]="cronicas";  //default
				     if($resul->category==1){$cat[]="cronicas";
						}elseif($resul->category==2){$cat1[$c][]="galicia";
						}elseif($resul->category==3){$cat1[$c][]="asturias";
						}elseif($resul->category==4){$cat1[$c][]="cronicas";
						}elseif($resul->category==5){$cat1[$c][]="canarias";
						}elseif($resul->category==6){$cat1[$c][]="castillaleon";
						}elseif($resul->category==7){$cat1[$c][]="madrid";
						}elseif($resul->category==8){$cat1[$c][]="baleares";
						}elseif($resul->category==9){$cat1[$c][]="andalucia";
						}
			  }
			  
        $tpl->assign('adjuntos1', $adjuntos1);
        $tpl->assign('cat1', $cat1);

}
$tpl->assign('column1', $column1);

$column2 = array();
$relat_c2=array();

for ( $c = 0 ,$aux = 0; $c <= $ArtPerColumn,$oddpublished[$aux]->title != ""; $c++, $aux ++ ) {
	$column2[$c] = $oddpublished[$aux];
	//related articles para column2
	$relationes = $relia->get_relations($oddpublished[$aux]->id);
	$i=0;
	
	foreach($relationes as $id_rel)
	{
		$relationed[$i]=$cm2->find('Article' , 'pk_content="'.$id_rel.'" ' , 'ORDER BY position DESC');	 
		$relat_c2[$c][]=$relationed[$i][0];
		$i++;
	}
	$tpl->assign('relationed_c2', $relat_c2);
	 
	 $attach_rel = new Attach_content();
	        $reles=array();
			$reles=$attach_rel->get_attach_relations($oddpublished[$aux]->id);
			
			  foreach($reles as $attaches) {				 
				     $resul = new Attachment($attaches);	      			
				     $adjuntos2[$c][]=$resul;	
				     $cat2[$c][]="cronicas";  //default
				     if($resul->category==1){$cat[]="cronicas";
						}elseif($resul->category==2){$cat2[$c][]="galicia";
						}elseif($resul->category==3){$cat2[$c][]="asturias";
						}elseif($resul->category==4){$cat2[$c][]="cronicas";
						}elseif($resul->category==5){$cat2[$c][]="canarias";
						}elseif($resul->category==6){$cat2[$c][]="castillaleon";
						}elseif($resul->category==7){$cat2[$c][]="madrid";
						}elseif($resul->category==8){$cat2[$c][]="baleares";
						}elseif($resul->category==9){$cat2[$c][]="andalucia";
						}
			  }
			  
        $tpl->assign('adjuntos2', $adjuntos2);
        $tpl->assign('cat2', $cat2);
}
$tpl->assign('column2', $column2);

//noticias mas vistas
$articles2 = $cm->find('Article', ' content_status=1 ', 'ORDER BY views DESC, created DESC LIMIT 0,10');
$tpl->assign('articles', $articles2);

$column3 = array($articles2[7]);
$tpl->assign('column3', $column3);

//Para cargar tpl principal
	$height=90;
	$width =140;
	$img1=$destacado->img1;			
		
	
	if($destacado->with_galery == 1){				
		$galery=array();
		$gal= new Img_galery();					
	  	$galery = $gal->read_galery_num( $articles[0]->id,1);
  		$img1=$galery[0][1];
	  	$tpl->assign('galery', $galery); 
	  	$result = count($galery);
	  	$tpl->assign('tantos', $result); 
  	}
  	$dimensions     = 	 new MediaItem("media/images/".$img1);
	$width    = $dimensions->width;    
	if($width<650){
	  if($width>300){
	    $width=300;
	  }
	}
  	$tpl->assign('width', $width);
	$height    = $dimensions->height;    
	$tpl->assign('height', $height);
	
//related articles para Destacado
 $rel= new Related_content();

 $relationes = $rel->get_relations($articles[0]->id);
 $relationed=array();
 $relat=array();

 $i=0;
 $cm2 = new ContentManager();
 foreach($relationes as $id_rel)
 {
	$relationed[$i]=$cm2->find('Article' , 'pk_content="'.$id_rel.'" ' , 'ORDER BY position DESC');
	 $relat[]=$relationed[$i][0];
	 $i++;
 }

 $tpl->assign('relationed', $relat);

   $attach_rel = new Attach_content();
	        $reles=array();
			$reles=$attach_rel->get_attach_relations($articles[0]->id);
			
			  foreach($reles as $attaches) {				 
				     $resul = new Attachment($attaches);	      			
				     $adjuntos[]=$resul;	
				     $cat[]="cronicas";  //default
				     if($resul->category==1){$cat[]="cronicas";
						}elseif($resul->category==2){$cat[]="galicia";
						}elseif($resul->category==3){$cat[]="asturias";
						}elseif($resul->category==4){$cat[]="cronicas";
						}elseif($resul->category==5){$cat[]="canarias";
						}elseif($resul->category==6){$cat[]="castillaleon";
						}elseif($resul->category==7){$cat[]="madrid";
						}elseif($resul->category==8){$cat[]="baleares";
						}elseif($resul->category==9){$cat[]="andalucia";
						}
			  }
			 
  $tpl->assign('adjuntos', $adjuntos);
  $tpl->assign('cat', $cat);
  
  
/*********************************PUBLICIDAD***********************************/
// type_advertisement: 1- Banner1, 2- Banner2, 3- Banner3, 4- B.Intermedio
// 5- Lateral, 6-Boton, 7-RobaP�gina
//fk_content_categories->si se quiere por categorias

$banner1 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=1' , 'ORDER BY created DESC');
$banner2 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=2', 'ORDER BY created DESC');
$banner3 = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=3', 'ORDER BY created DESC');
$binter = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=4', 'ORDER BY created DESC');
$buton = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=5', 'ORDER BY created DESC');
$lateral = $cm->find('Advertisement', ' content_status>0  AND type_advertisement=6', 'ORDER BY created DESC');

$result = count($banner1);
$r=rand(0,--$result);
$tpl->assign('banner1', $banner1[$r]);
$result = count($banner2);
$r=rand(0,--$result);
$tpl->assign('banner2', $banner2[$r]);
$result = count($banner3);
$r=rand(0,--$result);
$tpl->assign('banner3', $banner3[$r]);
$result = count($binter);
$r=rand(0,--$result);
$tpl->assign('binter', $binter[$r]);

$result = count($lateral);
$r=rand(0,--$result);
$tpl->assign('lateral', $lateral[$r]);

$result = count($buton);
$r=rand(0,--$result);
$tpl->assign('boton1', $buton[$r]);
$r=rand(0,--$result);
$tpl->assign('boton2', $buton[$r]);

/******FECHAS Y CALENDARIO HEMEROTECA*******/

$hoy=mktime(0,0,0,date(m),date(d),date(Y));
$tstamp = $_GET['stamp'];
if(empty($tstamp)) {$tstamp=$hoy; }
$thisMonth = (!empty($tstamp)) ? date('m', $tstamp) : date("m");
$thisYear = (!empty($tstamp)) ? date('Y', $tstamp) : date("Y");



$days_name = array('Domingo','Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado');


$month=$_GET['month'];
$year=$_GET['year'];

if($month == '' && $year == '') {
	$time = time();
	$month = date('n',$time);
    $year = date('Y',$time);
}

$data = date("d-m-Y",$hoy);

$tpl->assign('data', $data);
$dia=date('w',$hoy);
$tpl->assign('dia', $days_name[$dia]);

/************************************************/


$opinions = $cm->find('Opinion','content_status=1', 'ORDER BY created DESC LIMIT 0,6');
  //Le quitamos etiquetas html excepto <br> <p>
  foreach($opinions as $opp) {	  	
  	$opp->body=strip_tags($opp->body,"<br> <p>");
  	$opins[]=$opp;
  }
 
$tpl->assign('accordion', $opins);



$events = $cm->find('Event', 'content_status=1', 'ORDER BY created DESC LIMIT 0,6');
$tpl->assign('events', $events);



//Muestra noticia en columna
$tpl->assign('show', true);

// Pie de página
$tpl->assign('footer', 'Pie de página');

// Visualizar
$tpl->display('dinamicindex.tpl');

