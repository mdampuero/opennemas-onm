<?php
require_once('config.inc.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);


require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/article.class.php');
require_once('core/search.class.php');


require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');

require_once('core/video.class.php');
require_once('core/album.class.php');
/*
require_once('core/pc_content_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_photo.class.php');
require_once('core/pc_video.class.php');
require_once('core/pc_letter.class.php');
require_once('core/pc_opinion.class.php');
require_once('core/pc_user.class.php');
*/
  require_once('core/pc_poll.class.php');
 

/***********************************  HEADLINES  ***********************************************/

function convertir_fecha($fecha_datetime){
	//Esta función convierte la fecha del formato DATETIME de SQL
	//a formato DD-MM-YYYY HH:mm:ss
		$fecha = split("-",$row["fecha_datetime"]);
		$hora = split(":",$fecha[2]);
		$fecha_hora=split("",$hora[0]);
		$fecha_convertida=$fecha_hora[0].'-'.$fecha[1].'-'.$fecha[0].'
		'.$fecha_hora[1].':'.$hora[1].':'.$hora[2];
		return $fecha_convertida;
	}  
	
function sacar_hora($fecha_datetime){
	//Esta función convierte la fecha del formato DATETIME de SQL
	//a formato DD-MM-YYYY HH:mm:ss
	//	$fecha = split("-",$fecha_datetime);
	//	$hora = split(":",$fecha[2]);
		$tiempos=explode(" ",$fecha_datetime);
		$fecha_hora=explode(":",$tiempos[1]);
		$hora= $fecha_hora[0].':'.$fecha_hora[1];
	
		return $hora;
	}  	
$ccm = ContentCategoryManager::get_instance();
$cm = new ContentManager();
//FIXME: cambiar todos los echos por $tpl->fetch('crear.tpl');
if(isset($_REQUEST['action']) )
{
    switch($_REQUEST['action'])
    {
	case 'articles_express':		
            $articles_express = $cm->find_by_category_name('Article',$_GET['category_name'], 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 40');
            $articles_express = $cm->getInTime($articles_express);
            $articles_express=$cm->paginate_num($articles_express,5);
            if($articles_express){
                foreach ($articles_express as $as) {
                         echo '<div class="noticiaGaliciaTitulares">
                                <div class="iconoGaliciaTitulares"></div>
                                <div class="contTextoFilete">';
                        echo '<div class="textoGaliciaTitulares"><a href="'.$as->permalink.'">'.stripslashes($as->title).'</a></div>
                                <div class="fileteGaliciaTitulares"><img src="/themes/xornal/images/galiciaTitulares/fileteDashedGaliciaTitulares.gif" alt=""/></div>';
                         echo '	</div>
                                    </div> ';
                }
            }
            echo "";
            break;
	case 'articles_viewed':		
            $articles_viewed = $cm->find_by_category_name('Article',$_GET['category_name'], 'content_status=1 AND available=1 AND fk_content_type=1', 'ORDER BY views DESC');
            $articles_viewed = $cm->getInTime($articles_viewed);
            $articles_viewed=$cm->paginate_num($articles_viewed,8);
            if($articles_viewed){
                        foreach ($articles_viewed as $as) {
                                 echo '   <div class="CNoticiaMas">
                                                            <div class="CContainerIconoTextoNoticiaMas">
                                                                    <div class="iconoNoticiaMas"></div> ';
                                                                echo '<div class="textoNoticiaMas"><a href="'.$as->permalink.'">'.$as->title.'</a></div>
                                                                     </div>
                                                                <div class="fileteNoticiaMas"><img src="/themes/xornal/images/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
                                             echo '	</div> ';
                        }
            }
            echo "";
            break;

        case 'articles_home_express':
            $items_page=5;
            $page=$_REQUEST['page'];
            $_limit='LIMIT '.($page-1)*$items_page.', '.($items_page);
            $now= date('Y-m-d H:m:s',time()); //2009-02-28 21:00:13
            $articles_home_express = $cm->find('Article', 'content_status=1 AND available=1 AND fk_content_type=1 AND (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'")) ', 'ORDER BY created DESC '.$_limit);


            $articles_home_express = $cm->getInTime($articles_home_express);
            $params="'articles_home_express',''";
         //   $articles_home_express = $cm->paginate_num_js($articles_home_express,5, 2, "get_paginate_articles", $params);
           // $pages_home_express=$cm->pager;
            $pages_home_express =$cm->create_paginate(40, 5, 2, 'get_paginate_articles', $params);
            $html_out= "";
            if($articles_home_express){
                foreach ($articles_home_express as $as) {
                    $hora=sacar_hora($as->created);
                    $html_out.= '<div class="noticiaXPress">
                                                <div class="contHoraNoticiaXPress">
                                                    <div class="horaNoticiaXPress">'.$hora.'</div>
                                                    <div class="iconoRayoXPress"></div>
                                                </div>
                                                <div class="contTextoFilete">
                                                    <div class="textoNoticiaXPress">
                                                      <a href="'.$as->permalink.'">'.stripslashes($as->title).'</a></div>
                                                    <div class="fileteNoticiaXPress">
                                                      <img src="/themes/xornal/images/noticiasXPress/fileteDashedNoticiasXPress.gif" alt=""/></div>
                                                </div>
                                            </div>		';
                }
                $links=' <div class="CContenedorPaginado">
                        <div class="link_mas_nota">+ NoticiasXpress</div>
                        <div class="CPaginas">'.$pages_home_express->links.'</div>
                    </div>';
            }
            Application::ajax_out($html_out.$links);
            break;
		
        case 'deportes_express':
            $items_page=6;
            $page=$_REQUEST['page'];
            $_limit='LIMIT '.($page-1)*$items_page.', '.($items_page);
            $deportes_id=$ccm->get_id('deportes');
            $deportes_express = $cm->find_category_headline($deportes_id, 'available=1', 'ORDER BY changed DESC '.$_limit);
            //$deportes_express = $cm->find_by_category_name('Article','deportes', 'available=1 AND fk_content_type=1', 'ORDER BY changed DESC LIMIT 0 , 42');
            $deportes_express = $cm->getInTime($deportes_express);
            $params="'deportes_express','deportes'";
          //  $deportes_express = $cm->paginate_num_js($deportes_express,6, 1, "get_paginate_articles", $params);
         //   $pages_deportes_express=$cm->pager;

            $pager_deportes=$cm->create_paginate(42, $items_page, 1, 'javascript:get_paginate_articles', $params);
            $html_out= "";
            if($deportes_express){
                        foreach ($deportes_express as $as) {
                            $hora=sacar_hora($as->created);
                            $html_out.= '<div class="deporteXPress">
                                                          <div class="horaDeporteXPress">'. $hora.'</div>
                                                          <div class="contTextoFileteDeporte">
                                                              <div class="textoDeporteXPress"><a href="'.$as->permalink.'">'.stripslashes($as->title).'</a></div>
                                                              <div class="fileteDeporteXPress"><img src="/themes/xornal/images/deportesXPress/fileteDashedDeportesXPress.gif" alt="" /></div>
                                                        </div>
                                                    </div>		';
                        }
                       $links ='<div class="linkMasDeportes">+Deportes</div>
                                                            <div class="CPaginas">'.$pager_deportes->links.'</div>
                                                </div>';
            }
            Application::ajax_out($html_out.$links);

            break;
/*
        case 'titulares_galicia':
            $titulares_galicia = $cm->find_by_category_name('Article','galicia', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 3');
            $titulares_galicia = $cm->getInTime($titulares_galicia);
            $articles=$cm->paginate_num($titulares_galicia,3);
            break;

       	case 'titulares_espana':    
            $titulares_espana = $cm->find_by_category_name('Article','espana', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 3');
            $titulares_espana = $cm->getInTime($titulares_espana);
            $articles=$cm->paginate_num($titulares_espana,3);
            break;
       	case 'titulares_mundo':    
            $titulares_mundo = $cm->find_by_category_name('Article','mundo', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 3');
            $titulares_mundo = $cm->getInTime($titulares_mundo);
            $articles=$cm->paginate_num($titulares_mundo,3);
            break;
       	case 'titulares_economia':    
            $titulares_economia = $cm->find_by_category_name('Article','economia', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 3');
            $titulares_economia = $cm->getInTime($titulares_economia);
            $articles=$cm->paginate_num($titulares_economia,3);
            break;
       	case 'titulares_sociedad':    
            $titulares_sociedad = $cm->find_by_category_name('Article','sociedad', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 3');
            $titulares_sociedad = $cm->getInTime($titulares_sociedad);
            $articles=$cm->paginate_num($titulares_sociedad,3);
            break;
       	case 'titulares_cultura':    
            $titulares_cultura = $cm->find_by_category_name('Article','cultura', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 3');
            $titulares_cultura = $cm->getInTime($titulares_cultura);
            $articles=$cm->paginate_num($titulares_cultura,3);
            break;
       	case 'titulares_gente':    
            $titulares_gente = $cm->find_by_category_name('Article','gente', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 3');
            $titulares_gente = $cm->getInTime($titulares_gente);
            $articles=$cm->paginate_num($titulares_gente,3);
            break;
       	case 'titulares_salud':    
            $titulares_salud = $cm->find_by_category_name('Article','salud', 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY created DESC LIMIT 0 , 3');
            $titulares_salud = $cm->getInTime($titulares_salud);
            $articles=$cm->paginate_num($titulares_salud,3);
            break;
 *
 */
        case 'videos':            
            $others_videos =  $cm->find('Video', 'content_status=1', 'ORDER BY created DESC LIMIT 6, 29');
            $others_videos=$cm->paginate_num_js($others_videos,5, 1, 'get_paginate_articles',"'videos',''");
            $pages=$cm->pager;
            if($others_videos){
                                    $html_out=null;
                        foreach ($others_videos as $as) {
                            $html_out.="<div class=\"elementoListadoMediaPag\">";
                            $html_out.="	<div class=\"fotoElemMediaListado\" style=\"background-color:#000;\">";
                            $html_out.="		<span class=\"CEdgeThumbVideo\"></span>";
                            $html_out.="		<span class=\"CContainerThumbVideo\">";
                            $html_out.="			<img width=\"80\" alt=\"".stripslashes($as->title)."\" src=\"http://i4.ytimg.com/vi/".$as->videoid."/default.jpg\"/>";
                            $html_out.="		</span>";
                            $html_out.="	</div>";
                            $html_out.="	<div class=\"contSeccionFechaListado\">";
                            $html_out.="		<div class=\"seccionMediaListado\"><a href=\"".$as->permalink."\" style=\"color:#004B8D;\">".stripslashes($as->title)."</a></div>";
                            $html_out.="		<div class=\"fechaMediaListado\">".$as->changed."</div>";
                            $html_out.="	</div>";
                            $html_out.="	<div class=\"contTextoElemMediaListado\">";
                            $html_out.="		<div class=\"textoElemMediaListado\">";
                            $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->description)."</a>";
                            $html_out.="		</div>";
                            $html_out.="	</div>";
                            $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                            $html_out.="</div>";
                        }
                        $html_out.='<div class="posPaginadorGaliciaTitulares">
                                                            <div class="CContenedorPaginado">
                                                                    <div class="link_paginador">+ Videos: </div>
                                                                    <div class="CPaginas">
                                                                    '.$pages->links.'
                                                                    </div>
                                                            </div>
                                                    </div>';
                       Application::ajax_out($html_out);
        	}
		break;

        case 'albums':
          //  $list_albums = $cm->find('Album', 'available=1', 'ORDER BY pk_album DESC LIMIT 0 , 30');
          $list_albums = $cm->find_by_category('Album', 3, 'available=1', 'ORDER BY created DESC LIMIT 0 , 30');       
            $list_albums=$cm->paginate_num_js($list_albums,5, 1, 'get_paginate_articles',"'albums',''");
            $pages=$cm->pager;
            if($list_albums){
                                    $html_out=null;
                        foreach ($list_albums as $as) {
                                            $html_out.="<div class=\"elementoListadoMediaPag\">";
                                            $html_out.="   <div class=\"fotoElemMedia\">";
                                            $html_out.="    <img style='height:88px;' src='/media/images/album/crops/".$as->id.".jpg'>";
                                            $html_out.="   </div>";
                                            $html_out.="	<div class=\"contSeccionFechaListado\">";
                                            $html_out.="		<div class=\"seccionMediaListado\"><a href=\"".$as->permalink."\" style=\"color:#004B8D;\">".stripslashes($as->title)."</a></div>";
                                            $html_out.="		<div class=\"fechaMediaListado\">".$as->created."</div>";
                                            $html_out.="	</div>";
                                            $html_out.="	<div class=\"contTextoElemMediaListado\">";
                                            $html_out.="		<div class=\"textoElemMediaListado\">";
                                            $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->description)."</a>";
                                            $html_out.="		</div>";
                                            $html_out.="	</div>";
                                            $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                                            $html_out.="</div>";
                        }
                        $html_out.='<div class="posPaginadorGaliciaTitulares">
                                                                    <div class="CContenedorPaginado">
                                                                            <div class="link_paginador">+ Albums </div>
                                                                            <div class="CPaginas">
                                                                            '.$pages->links.'
                                                                            </div>
                                                                    </div>
                                                            </div>';
                        Application::ajax_out($html_out);
                }
                
                break;
/*
        case 'pc_foto-dia':
            $cp = new PC_ContentManager();
            $arrayPhotos = array();
            if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                            $arrayPhotos = $cp->find_by_category_name('PC_Photo','foto-dia','available=1 and pk_pc_content <> '.$_REQUEST["id"].'', 'ORDER BY content_status DESC, changed DESC LIMIT 0,30');
                    }else{
                            $arrayPhotos = $cp->find_by_category_name('PC_Photo', 'foto-dia', 'available=1', 'ORDER BY content_status DESC, changed DESC LIMIT 0,30');
                            $_REQUEST['id']=$arrayPhotos[0]->id;
                            array_shift($arrayPhotos);
                    }
                    $params="'".$_REQUEST['action']."','','".$_REQUEST['id']."'";
                    $arrayPhotos=$cm->paginate_num_js($arrayPhotos,5, 1, 'get_paginate_pc',$params);
                    $pages=$cm->pager;
                    if($arrayPhotos){
                                            $html_out=null;
                                foreach ($arrayPhotos as $as) {
                                                    $html_out.="<div class=\"elementoListadoMediaPag\">";
                                                    $html_out.="	<div class=\"fotoElemMediaListado\">";
                                                    $html_out.="	    <img style=\"width:78px;height:94px;\" src=\"".MEDIA_CONECTA_WEB.$as->path_file."\">";
                                                    $html_out.="	</div>";
                                                    $html_out.="	<div class=\"contSeccionFechaListado\">";
                                                    $html_out.="		<div class=\"seccionMediaListado\"><a href=\"".$as->permalink."\" style=\"color:#004B8D;\">".stripslashes($as->title)."</a></div>";
                                                    $html_out.="		<div class=\"fechaMediaListado\">".$as->created."</div>";
                                                    $html_out.="	</div>";
                                                    $html_out.="	<div class=\"contTextoElemMediaListado\">";
                                                    $html_out.="		<div class=\"textoElemMediaListado\">";
                                                    $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->description)."</a>";
                                                    $html_out.="		</div>";
                                                    $html_out.="	</div>";
                                                    $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                                                    $html_out.="</div>";
                                }
                                 $html_out.='<div class="posPaginadorGaliciaTitulares">
                                                                            <div class="CContenedorPaginado">
                                                                                    <div class="link_paginador">+ Fotos</div>
                                                                                    <div class="CPaginas">
                                                                                    '.$pages->links.'
                                                                                    </div>
                                                                            </div>
                                                                    </div>';
                Application::ajax_out($html_out);
             }
             break;
        case 'pc_foto-denuncia':
        		$cp = new PC_ContentManager();
                $arrayPhotos = array();
                if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])){  
                        $arrayPhotos = $cp->find_by_category_name('PC_Photo','foto-denuncia','available=1 and pk_pc_content <> '.$_REQUEST["id"].'', 'ORDER BY content_status DESC, changed DESC LIMIT 0,30');
                }else{
                        $arrayPhotos = $cp->find_by_category_name('PC_Photo', 'foto-denuncia', 'available=1', 'ORDER BY content_status DESC, changed DESC LIMIT 0,30');
                        $_REQUEST['id']=$arrayPhotos[0]->id;
                        array_shift($arrayPhotos);
                }
                $params="'".$_REQUEST['action']."','','".$_REQUEST['id']."'";
                $arrayPhotos=$cm->paginate_num_js($arrayPhotos,5, 1, 'get_paginate_pc',$params);
                $pages=$cm->pager;
                if($arrayPhotos){
                    $html_out=null;
                    foreach ($arrayPhotos as $as) {
                            $html_out.="<div class=\"elementoListadoMediaPag\">";
                            $html_out.="	<div class=\"fotoElemMediaListado\">";
                            $html_out.="	    <img style=\"width:78px;height:94px;\" src=\"".MEDIA_CONECTA_WEB.$as->path_file."\">";
                            $html_out.="	</div>";
                            $html_out.="	<div class=\"contSeccionFechaListado\">";
                            $html_out.="		<div class=\"seccionMediaListado\"><a href=\"".$as->permalink."\" style=\"color:#004B8D;\">".stripslashes($as->title)."</a></div>";
                            $html_out.="		<div class=\"fechaMediaListado\">".$as->created."</div>";
                            $html_out.="	</div>";
                            $html_out.="	<div class=\"contTextoElemMediaListado\">";
                            $html_out.="		<div class=\"textoElemMediaListado\">";
                            $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->description)."</a>";
                            $html_out.="		</div>";
                            $html_out.="	</div>";
                            $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                            $html_out.="</div>";
                    }
                    $html_out.='<div class="posPaginadorGaliciaTitulares">
                                <div class="CContenedorPaginado">
                                        <div class="link_paginador">+ Fotos</div>
                                        <div class="CPaginas">
                                        '.$pages->links.'
                                        </div>
                                </div>
                        </div>';
                    Application::ajax_out($html_out);
        	    }
				break;
        case 'pc_video-dia':
                $cp = new PC_ContentManager();
                $arrayVideos = array();
                if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                        $arrayVideos = $cp->find_by_category_name('PC_Video', 'video-dia','available=1 and pk_pc_content <> '.$_REQUEST["id"], 'ORDER BY content_status DESC, changed DESC LIMIT 0,30');

                        //print_r($arrayVideos);exit();
                }else{
                        $arrayVideos = $cp->find_by_category_name('PC_Video', 'video-dia', 'available=1', 'ORDER BY content_status DESC, changed DESC  LIMIT 0,30');
                        $_REQUEST['id']=$arrayVideos[0]->id;
                        array_shift($arrayVideos);
                }
                $params="'".$_REQUEST['action']."','','".$_REQUEST['id']."'";
                $arrayVideos=$cm->paginate_num_js($arrayVideos,5, 1, 'get_paginate_pc',$params);
                $pages=$cm->pager;
                if($arrayVideos){
                    $html_out=null;
                    foreach ($arrayVideos as $as) {
                        $html_out.="<div class=\"elementoListadoMediaPag\">";
                        $html_out.="	<div class=\"fotoElemMediaListado\">";
                        $html_out.="	    <img style=\"width:78px;height:94px;\" src=\"http://i4.ytimg.com/vi/".$as->code."/default.jpg\">";
                        $html_out.="	</div>";
                        $html_out.="	<div class=\"contSeccionFechaListado\">";
                        $html_out.="		<div class=\"seccionMediaListado\"><a href=\"".$as->permalink."\" style=\"color:#004B8D;\">".stripslashes($as->title)."</a></div>";
                        $html_out.="		<div class=\"fechaMediaListado\">".$as->created."</div>";
                        $html_out.="	</div>";
                        $html_out.="	<div class=\"contTextoElemMediaListado\">";
                        $html_out.="		<div class=\"textoElemMediaListado\">";
                        $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->description)."</a>";
                        $html_out.="		</div>";
                        $html_out.="	</div>";
                        $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                        $html_out.="</div>";
                    }
                    $html_out.='<div class="posPaginadorGaliciaTitulares">
                                    <div class="CContenedorPaginado">
                                            <div class="link_paginador">+ Videos</div>
                                            <div class="CPaginas">
                                            '.$pages->links.'
                                            </div>
                                    </div>
                            </div>';
                    Application::ajax_out($html_out);
        	    }
		break;
        case 'pc_video-denuncia':
                $cp = new PC_ContentManager();
                $arrayVideos = array();
                if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                        $arrayVideos = $cp->find_by_category_name('PC_Video', 'video-denuncia','available=1 and pk_pc_content <> '.$_REQUEST["id"].'', 'ORDER BY content_status DESC, changed DESC LIMIT 0,30');
                }else{
                        $arrayVideos = $cp->find_by_category_name('PC_Video', 'video-denuncia', 'available=1', 'ORDER BY content_status DESC, changed DESC  LIMIT 0,30');
                        $_REQUEST['id']=$arrayVideos[0]->id;
                        array_shift($arrayVideos);
                }
                $params="'".$_REQUEST['action']."','','".$_REQUEST['id']."'";
                $arrayVideos=$cm->paginate_num_js($arrayVideos,5, 1, 'get_paginate_pc',$params);
                $pages=$cm->pager;
                if($arrayVideos){
                            $html_out=null;
                            foreach ($arrayVideos as $as) {
                                $html_out.="<div class=\"elementoListadoMediaPag\">";
                                $html_out.="	<div class=\"fotoElemMediaListado\">";
                                $html_out.="	    <img style=\"width:78px;height:94px;\" src=\"http://i4.ytimg.com/vi/".$as->code."/default.jpg\">";
                                $html_out.="	</div>";
                                $html_out.="	<div class=\"contSeccionFechaListado\">";
                                $html_out.="		<div class=\"seccionMediaListado\"><a href=\"".$as->permalink."\" style=\"color:#004B8D;\">".stripslashes($as->title)."</a></div>";
                                $html_out.="		<div class=\"fechaMediaListado\">".$as->created."</div>";
                                $html_out.="	</div>";
                                $html_out.="	<div class=\"contTextoElemMediaListado\">";
                                $html_out.="		<div class=\"textoElemMediaListado\">";
                                $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->description)."</a>";
                                $html_out.="		</div>";
                                $html_out.="	</div>";
                                $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                                $html_out.="</div>";
                            }
                            $html_out.='<div class="posPaginadorGaliciaTitulares">
                                            <div class="CContenedorPaginado">
                                                    <div class="link_paginador">+ Videos</div>
                                                    <div class="CPaginas">
                                                    '.$pages->links.'
                                                    </div>
                                            </div>
                                    </div>';
                            Application::ajax_out($html_out);
        	    }
		break;
 * /
       
  /*
        case 'pc_letter':
                $cp = new PC_ContentManager();
                $arrayletters = array();
                if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                     $arrayletters = $cp->find('PC_Letter', 'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status DESC,  changed DESC LIMIT 0, 30');
                }else{
                    $arrayletters = $cp->find('PC_Letter', 'available=1', 'ORDER BY content_status DESC,  changed DESC LIMIT 0, 30');
                    $_REQUEST['id']=$arrayletters[0]->id;
                    array_shift($arrayletters);
                }
                $params="'pc_letter','','".$_REQUEST['id']."'";
                $arrayletters=$cm->paginate_num_js($arrayletters,5, 1, 'get_paginate_pc',$params);
                $pages=$cm->pager;
                if($arrayletters){
                           $html_out=null;
                            foreach ($arrayletters as $as) {
                                $html_out.="<div class=\"elementoListadoMediaPag\">";
                                $html_out.="	<div class=\"contSeccionFechaListado\">";
                                $html_out.="		<div class=\"seccionMediaListado\"><a href=\"/conecta/".$as->permalink.".html\" style=\"color:#004B8D;\">".stripslashes($as->author)."</a></div>";
                                $html_out.="		<div class=\"fechaMediaListado\">".$as->created."</div>";
                                $html_out.="	</div>";
                                $html_out.="	<div class=\"contTextoElemMediaListado\">";
                                $html_out.="		<div class=\"textoElemMediaListado\">";
                                $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->title)."</a>";
                                $html_out.="		</div>";
                                $html_out.="	</div>";
                                $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                                $html_out.="</div>";
                            }
                            $html_out.='<div class="posPaginadorGaliciaTitulares">
                                            <div class="CContenedorPaginado">
                                                    <div class="link_paginador">+ Cartas</div>
                                                    <div class="CPaginas">
                                                    '.$pages->links.'
                                                    </div>
                                            </div>
                                    </div>';
                            Application::ajax_out($html_out);
				            
        	    }
		break;
	case 'pc_opinion':
                $cp = new PC_ContentManager();
                $arrayopinions = null;
                if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                        $arrayopinions = $cp->find('PC_Opinion', 'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status DESC,  changed DESC LIMIT 0, 30');
                }else{
                        $arrayopinions = $cp->find('PC_Opinion', 'available=1', 'ORDER BY content_status DESC,  changed DESC LIMIT 0, 30');
                        $_REQUEST['id']=$arrayopinions[0]->id;
                        array_shift($arrayopinions);
                }
                $params="'pc_opinion','','".$_REQUEST['id']."'";
                $arrayopinions=$cm->paginate_num_js($arrayopinions,5, 1, 'get_paginate_pc',$params);
                $pages=$cm->pager;
                if($arrayopinions){
                    $html_out=null;
                    foreach ($arrayopinions as $as) {
                        $html_out.="<div class=\"elementoListadoMediaPag\">";
                        $html_out.="	<div class=\"contSeccionFechaListado\">";
                        $html_out.="		<div class=\"seccionMediaListado\"><a href=\"/conecta/".$as->permalink.".html\" style=\"color:#004B8D;\">".stripslashes($as->author)."</a></div>";
                        $html_out.="		<div class=\"fechaMediaListado\">".$as->created."</div>";
                        $html_out.="	</div>";
                        $html_out.="	<div class=\"contTextoElemMediaListado\">";
                        $html_out.="		<div class=\"textoElemMediaListado\">";
                        $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->title)."</a>";
                        $html_out.="		</div>";
                        $html_out.="	</div>";
                        $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                        $html_out.="</div>";
                    }
                    $html_out.='<div class="posPaginadorGaliciaTitulares">
                                    <div class="CContenedorPaginado">
                                            <div class="link_paginador">+ Opiniones</div>
                                            <div class="CPaginas">
                                            '.$pages->links.'
                                            </div>
                                    </div>
                            </div>';
                    Application::ajax_out($html_out);

                }
            break;
 */
    case 'pc_polls':
                $cp = new PC_ContentManager();
                $arrayPolls = array();
                if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                        $arrayPolls = $cp->find('PC_Poll', 'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status DESC, changed DESC LIMIT 0, 30');
                }else{
                        $arrayPolls = $cp->find('PC_Poll', 'available=1', 'ORDER BY content_status DESC, changed DESC LIMIT 0, 30');
                        $_REQUEST['id']=$arrayPolls[0]->id;
                        array_shift($arrayPolls);
                }
                $params="'".$_REQUEST['action']."','','".$_REQUEST['id']."'";
                $arrayPolls=$cm->paginate_num_js($arrayPolls,5, 1, 'get_paginate_pc',$params);
                $pages=$cm->pager;
                if($arrayPolls){
                        $html_out=null;
                            foreach ($arrayPolls as $as) {
                                $html_out.="<div class=\"elementoListadoMediaPag\">";
                                $html_out.="	<div class=\"contSeccionFechaListado\">";
                                $html_out.="		<div class=\"seccionMediaListado\"><a href=\"/conecta/enquisa/".$as->id.".html\" style=\"color:#004B8D;\">".stripslashes($as->subtitle)."</a></div>";
                                $html_out.="		<div class=\"fechaMediaListado\">".$as->changed."</div>";
                                $html_out.="	</div>";
                                $html_out.="	<div class=\"contTextoElemMediaListado\">";
                                $html_out.="		<div class=\"textoElemMediaListado\">";
                                $html_out.="			<a href=\"".$as->permalink."\">".stripslashes($as->title)."</a>";
                                $html_out.="		</div>";
                                $html_out.="	</div>";
                                $html_out.="	<div class=\"fileteIntraMedia\"></div>";
                                $html_out.="</div>";

                            }
                            $html_out.='<div class="posPaginadorGaliciaTitulares">
                                            <div class="CContenedorPaginado">
                                                    <div class="link_paginador">+ Encuestas</div>
                                                    <div class="CPaginas">
                                                    '.$pages->links.'
                                                    </div>
                                            </div>
                                    </div>';
                            Application::ajax_out($html_out);
        	    }
		break;
    }

  
}