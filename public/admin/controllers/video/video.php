<?php

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

$content_types = array('article' => 1 , 'album' => 7, 'video' => 9, 'opinion' => 4, 'kiosko'=>14);

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Video Management');
 
 // Check if the user can admin album
Acl::checkOrForward('VIDEO_ADMIN');

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);

/******************* GESTION CATEGORIAS  *****************************/

$category = filter_input(INPUT_GET,'category',FILTER_VALIDATE_INT);
if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT);

}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($content_types['video'], $category);

if (empty($category) ) {
    $category = $categoryData[0]->pk_content_category;
}

$tpl->assign('category', $category);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
//TODO: ¿datoscat?¿
$tpl->assign('datos_cat', $categoryData);


 
/* GESTION CATEGORIAS  */

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':  //Buscar publicidad entre los content
			$cm = new ContentManager();

		     	// ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
                list($videos, $pager)= $cm->find_pages('Video', 'fk_content_type=9 ', 'ORDER BY  created DESC ',$page,10, $category);
                $i=0;
 

            foreach($videos as $video){
                $video->category_name = $video->loadCategoryName($video->pk_content);
                $i++;
            }
			/* Ponemos en la plantilla la referencia al objeto pager */
			$tpl->assign('paginacion', $pager);
			$tpl->assign('videos', $videos);
		 

             
		break;

		case 'new':
			// Nada

		break;

		case 'read': //habrá que tener en cuenta el tipo
			$video = new Video( $_REQUEST['id'] );
			if($video->author_name =='vimeo'){
                $url="  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                $curl = curl_init( 'http://vimeo.com/api/v2/video/'.$video->videoid.'.php');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                $return = curl_exec($curl);
                $return = unserialize($return);
                curl_close($curl);
                $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                $video->thumbnail_small = $return[0]['thumbnail_small'];
            }
            $tpl->assign('video', $video);
		  
		break;

		case 'update':
			$video = new Video();			
			$video->update( $_REQUEST );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_POST['category'].'&page='.$page);
		break;

		case 'create':
			$video = new Video();
			if($video->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_POST['category'].'&page='.$page);
			} else {
				$tpl->assign('errors', $video->errors);
			}
		break;
		
		case 'validate':
			$video = null;
			if(empty($_POST["id"])) {
				$video = new Video();
				//Estamos creando un nuevo artículo
				if(!$video->create( $_POST ))
					$tpl->assign('errors', $video->errors);		
			} else {
				$video = new Video($_POST["id"]);
				//Estamos atualizando un artículo
				$video->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$video->id);
		break;
 
        case 'delete':
			$video = new Video($_REQUEST['id']);
                        $rel= new Related_content();
                        $relationes=array();
                        $msg ='';
                        $relationes = $rel->get_content_relations( $_REQUEST['id'] );//de portada
                        if(!empty($relationes)){
                             $msg = "El video '".$video->title."', está relacionado con los siguientes articulos:  ";
                             $cm= new ContentManager();
                             $relat = $cm->getContents($relationes);
                             foreach($relat as $contents) {
                                   $msg.="\n - ".strtoupper($contents->category_name).": ".$contents->title;
                             }
                             $msg.="\n \n ¡Ojo! Si lo borra, se eliminaran las relaciones del video con los articulos";
                             $msg.="\n ¿Desea eliminarlo igualmente?";
                          //   $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                           //  $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                             echo $msg;
                             exit(0);
                        }else{
                            $msg.="¿Está seguro que desea eliminar '".$video->title."' ?";
                           // $msg.='\n <a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                          //  $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                            echo $msg;
                            exit(0);
                        }
		break;

                case 'yesdel':
                    if($_REQUEST['id']){
                       $video = new Video($_REQUEST['id']);
                        //Delete relations
                        $rel= new Related_content();
                        $rel->delete_all($_REQUEST['id']);
                        $video->delete( $_REQUEST['id'],$_SESSION['userid'] );
                    }
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);
                 break;
		
		case 'set_position':
			$video = new Video($_REQUEST['id']);
			$video->set_position($_REQUEST['posicion'],$_SESSION['userid']);
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);
		break;

		case 'change_status':
			$video = new Video($_REQUEST['id']);
			//Publicar o no, 
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			//$video->set_status($status,$_SESSION['userid']);
			$video->set_available($status, $_SESSION['userid']);
			
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);
		break;

        case 'change_favorite':
            $video = new Video($_REQUEST['id']);
            $msg = '';
             //Publicar o no,
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            if($video->available==1){
                    $video->set_favorite($status);
            }else{
                    $msg="No se puede esta despublicado";
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$category);
		break;

	
		case 'mfrontpage':		
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			    	$fields = $_REQUEST['selected_fld'];
					$status = ($_REQUEST['status']==1)? 1: 0; 
		            if(is_array($fields)) {
					      foreach($fields as $i ) {
					        $video = new Video($i);
					        //$video->set_status($_REQUEST['id'],$_SESSION['userid']);   //Se reutiliza el id para pasar el estatus
					        $video->set_available($status, $_SESSION['userid']); 
					        }
        		    }
			  }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
		break;
		
		case 'mdelete':
		
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			    $fields = $_REQUEST['selected_fld'];
                            if(is_array($fields)) {
                                  $msg="Los videos ";
                                 $alert='';
				       foreach($fields as $i ) {
					        $video = new Video($i);	
                                                $rel= new Related_content();
                                                $relationes=array();

                                                $relationes = $rel->get_content_relations( $i );//de portada

                                                if(!empty($relationes)){
                                                     $nodels[] =$i;
                                                     $alert='ok';
                                                     $msg .= " \"".$video->title."\", ";

                                                }else{
                                                   $video->delete( $i,$_SESSION['userid'] );
                                                 }
                                       }
                             }
			  }
                               $msg.=" tiene relacionados.  !Eliminelos uno a uno!";
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&alert='.$alert.'&msgdel='.$msg.'&page='.$page);
		break;
		
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
}

$tpl->display('video/video.tpl');
?>
