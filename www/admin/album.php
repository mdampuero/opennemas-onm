<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

// Register events
require_once('albums_events.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Album de fotografías');

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');

require_once('core/album.class.php');
require_once('core/photo.class.php');
require_once('core/album_photo.class.php');
require_once('core/privileges_check.class.php');


if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 3;}
$tpl->assign('category', $_REQUEST['category']);

if( !Privileges_check::CheckPrivileges('MUL_ADMIN'))
{
    Privileges_check::AccessDeniedAction();
} 
/* GESTION CATEGORIAS */
$ccm = new ContentCategoryManager();
$allcategorys = $ccm->find('(internal_category=1 OR internal_category=3) AND fk_content_category=0', 'ORDER BY internal_category DESC, posmenu');
//var_dump($allcategorys);
$i=0;
foreach( $allcategorys as $prima) {
    $subcat[$i]=$ccm->find(' inmenu=1  AND internal_category=1 AND fk_content_category ='.$prima->pk_content_category, 'ORDER BY posmenu');
      $i++;
}

$datos_cat = $ccm->find('pk_content_category='.$_REQUEST['category'], NULL);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $allcategorys);
$tpl->assign('datos_cat', $datos_cat);

//Para album_images
list($othercategorys, $othersubcat, $datos_cat) = $ccm->getArraysMenu();

$tpl->assign('othercategorys', $othercategorys);
$tpl->assign('othersubcat', $othersubcat);
/* GESTION CATEGORIAS  */

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':  //Buscar publicidad entre los content
			$cm = new ContentManager();
			// ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);                        
			list($albums, $pager)= $cm->find_pages('Album', 'fk_content_type=7 ', 'ORDER BY  created DESC ',$_REQUEST['page'],10, $_REQUEST['category']); 
                        $favorito = $cm->find_by_category('Album', 3, 'fk_content_type=7 and available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0 , 1');
                        if(!$favorito[0]){                          
                           $album=new Album($albums[0]->id);
                            $album->set_favorite($status);
                            $albums[0]->favorite=1;
                        }
                     
                        $tpl->assign('albums', $albums);
			$tpl->assign('paginacion', $pager);

		break;

		case 'new':
			$cm = new ContentManager(); //Por ahora solo se puede subir a album
		//	$photos = $cm->find_by_category('Photo',3, 'fk_content_type=8 ', 'ORDER BY created DESC');
                        list($photos, $pager)= $cm->cache->find_pages('Photo', 'contents.fk_content_type=8 and photos.media_type="image"', 'ORDER BY  created DESC ',$_REQUEST['page'],30);

			foreach($photos as $photo){
				if(file_exists(MEDIA_IMG_PATH.$photo->path_file.$photo->name)){
					$photo->content_status=1;
					$ph=new Photo($photo->pk_photo);
					$ph->set_status(1,$_SESSION['userid']);
				}else{
					$photo->content_status=0;
					$ph=new Photo($photo->pk_photo);
					$ph->set_status(0,$_SESSION['userid']);
				}
			}
			$tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);
			//$photos=$cm->paginate_num_js($photos, 30, 3, 'get_images_album','NULL');
			$tpl->assign('photos', $photos);
			/*$pages=$cm->pager;
			if($pages->_totalPages>1){
		 		$tpl->assign('paginacion', $pages);
			}	                         
                         */
                        $pages=$pager;

                        $paginacion=$cm->makePagesLinkjs($pages,'get_images_album','\'album\'');
                        if($pages->_totalPages>1) {
                                $tpl->assign('paginacion', $paginacion);
                        }
		break;

		case 'read': //habrá que tener en cuenta el tipo
			$cm = new ContentManager();
			$album = new Album( $_REQUEST['id'] );
			$tpl->assign('album', $album);
			$crop_exist=file_exists('../media/images/album/crops/'.$_REQUEST['id'].'.jpg');
                        $tpl->assign('crop_exist', $crop_exist);
			$oldphoto=array();
  		 	$oldphotos = $album->get_album($_REQUEST['id']);
  		 	$tpl->assign('oldphotos', $oldphotos);

  		 	foreach($oldphotos as $ph){
  		 		$oldphoto[] = new Photo($ph[0]);
  		 	}
  		 	$tpl->assign('oldphoto', $oldphoto);
  		 	
			//$photos = $cm->find_by_category('Photo', 3, 'fk_content_type=8 ', 'ORDER BY created DESC');
                        list($photos, $pager)= $cm->cache->find_pages('Photo', 'fk_content_type=8 and photos.media_type="image"', 'ORDER BY  created DESC ',$_REQUEST['page'],30);
			foreach($photos as $photo) {
				if(file_exists(MEDIA_IMG_PATH.$photo->path_file.$photo->name)) {
					$photo->content_status=1;
					$ph=new Photo($photo->pk_photo);
					$ph->set_status(1,$_SESSION['userid']);
				}else{
					$photo->content_status=0;
					$ph=new Photo($photo->pk_photo);
					$ph->set_status(0,$_SESSION['userid']);
				}
			}
			$tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);
			
			 $photos=$cm->paginate_num_js($photos, 30, 3, 'get_images_album','NULL');
			$tpl->assign('photos', $photos);
			/*$pages=$cm->pager;
			if($pages->_totalPages>1){
		 		$tpl->assign('paginacion', $pages);
			}
                         * 
                         */
                      $pages=$pager;

                        $paginacion=$cm->makePagesLinkjs($pages,'get_images_album','\'album\'');
                        if($pages->_totalPages>1) {
                                $tpl->assign('paginacion', $paginacion);
                        }
                     

		break;

		case 'update':
			$album = new Album();
			$album->update( $_REQUEST );                                              
                       
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;

		case 'create':
			$album = new Album();
			if($album->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
			}else{
				$tpl->assign('errors', $album->errors);
			}
		break;

		case 'delete':
			$album = new Album($_REQUEST['id']);
                        $rel= new Related_content();
                        $relationes=array();
                        $relationes = $rel->get_content_relations( $_REQUEST['id'] );//de portada
                        if(!empty($relationes)){
                             $msg = "El album  '".$album->title."' , está relacionado con los siguientes articulos:  \n";
                             $cm= new ContentManager();
                             $relat = $cm->getContents($relationes);
                             foreach($relat as $contents) {
                                   $msg.=" - ".strtoupper($contents->category_name).": ".$contents->title.'\n';
                             }
                             $msg.="\n \n ¡Ojo! Si lo borra, se eliminar&aacute;n las relaciones del album con los articulos";
                             $msg.="\n ¿Desea eliminarlo igualmente?";
                           //  $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                            // $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                             echo $msg;
                             exit(0);
                        }else{
                            $msg.="¿Está seguro que desea eliminar  '".$album->title."' ?";
                          //  $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                          //  $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                            echo $msg;
                            exit(0);
                        }
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;

                case 'yesdel':
                    if($_REQUEST['id']){
                       $album = new Album($_REQUEST['id']);
                        //Delete relations
                        $rel= new Related_content();
                        $rel->delete_all($_REQUEST['id']);
                        $album->delete( $_REQUEST['id'],$_SESSION['userid'] );
                    }
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$album->category.'&page='.$_REQUEST['page']);
                 break;

		case 'validate':
			$album = null;
			if(empty($_POST["id"])) {
				$album = new Album();
				if(!$album->create( $_POST ))
					$tpl->assign('errors', $album->errors);		
			} else {
				$album = new Album($_POST["id"]);
				$album->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$album->id);
		break;
		
		case 'change_status':
			$album = new Album($_REQUEST['id']);

			//Publicar o no, comprobar num clic
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			//$album->set_status($status,$_SESSION['userid']);

                        $album->set_available($status, $_SESSION['userid']);

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
		break;

                case 'change_favorite':
			$album = new Album($_REQUEST['id']);                                              
                             //Publicar o no,
                            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                            if($album->available==1){
                                    $album->set_favorite($status);
                            }else{
                                    $msg="No se puede esta despublicado";
                            }
                      Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$_REQUEST['category']);
		break;

		case 'mfrontpage':
                    if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
                    {
                        $fields = $_REQUEST['selected_fld'];
                        $status = ($_REQUEST['id']==1)? 1: 0; // Evitar otros valores   //Se reutiliza el id para pasar el estatus
                        if(is_array($fields))
                        {
                            foreach($fields as $i )
                            {
                                $album = new Album($i);
                                $album->set_available($status, $_SESSION['userid']);
                            }
                        }
                    }

                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
		break;
		
		case 'mdelete':
			if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			{
			    $fields = $_REQUEST['selected_fld'];
                            if(is_array($fields))
                            {
                                $msg="Los albumes ";                   
                                 $alert='';
                                foreach($fields as $i )
                                {
                                    $album = new Album($i);
                                    $rel= new Related_content();
                                    $relationes=array();

                                    $relationes = $rel->get_content_relations( $i );//de portada

                                    if(!empty($relationes)){
                                         $nodels[] =$i;
                                         $alert='ok';
                                         $msg .= " \"".$album->title."\", ";

                                    }else{
                                       $album->delete( $i,$_SESSION['userid'] );
                                    }
                                }
                            }
                        }
                         $msg.=" tiene relacionados.  !Eliminelos uno a uno!";
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&alert='.$alert.'&msgdel='.$msg.'&page='.$_REQUEST['page']);
		break;
				

                case 'crop_image':      
                    $image=$_REQUEST['id_photo'].'jpg';
                    $picture = new Imagick('../media/images/album/castle.jpg');              
                    $width='250';
                    $height='250';   
                    $x='20';            //$_REQUEST['x'];
                    $y='20';            //$_REQUEST['y'];
                    $picture->cropImage($width, $height, $x, $y);
                    $picture->writeImage('../media/images/album/test.jpg'); //$_REQUEST['id_album'].'jpg';
                    exit(0);
		    break;
                
		case 'get_images_album':
			// Container galery photos.
			$cm = new ContentManager();
			$tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);
			//$photos = $cm->find_by_category('Photo',3, 'fk_content_type=8 ', 'ORDER BY created DESC');
			//$photos=$cm->paginate_num_js($photos, 30, 3, 'get_images_album','NULL');
			list($photos, $pager)= $cm->find_pages('Photo', 'fk_content_type=8 and photos.media_type="image"', 'ORDER BY  created DESC ',$_REQUEST['page'],30);
                        $tpl->assign('photos', $photos);
			//$pages=$cm->pager;
                        $pages=$pager;
                        $paginacion=$cm->makePagesLinkjs($pages,'get_images_album','\'album\'');

				 echo "<em> Pinche y arrastre las imagenes para seleccionarlas</em><br><br>";				
				 if($pages->_totalPages>1) {
			       	echo '<p align="center">'  ;
				//	echo $pages->links;
                                        echo $paginacion;
					echo '</p>';
				 }
				 echo "<ul id='thelist'  class='gallery_list' style='width:400px;'> ";				   
				 foreach ($photos as $as) {						
                                    if(file_exists(MEDIA_IMG_PATH.$as->path_file.$as->name)){
                                        $ph=new Photo($as->pk_photo);
                                        if(strtolower($as->type_img) !='swf'){
                                            $ph->set_status(1,$_SESSION['userid']);

                                            require( dirname(__FILE__).'/themes/default/plugins/function.cssimagescale.php' );
                                            $params = array('media' => MEDIA_IMG_PATH, 'photo' => $as, 'resolution' => 67);
                                            $params2 = array('media' => MEDIA_IMG_PATH, 'photo' => $as, 'resolution' => 67, 'getwidth'=>1);
                                            if(strtolower($as->type_img) =='jpg' || strtolower($as->type_img) =='jpg'){
                                                echo '<li><div style="float: left;"> <a>'.
                                                     '<img style="'.smarty_function_cssimagescale($params).'"  de:width="'.smarty_function_cssimagescale($params2).'" src="'.MEDIA_IMG_PATH_WEB.$as->path_file.'140x100-'.$as->name.'" id="draggable_img'.$num.'" class="draggable" name="'.$as->pk_photo.'"  de:path="'.$as->path_file.'" border="0" de:mas="'.$as->name.'" de:ancho="'.$as->width.'" de:alto="'.$as->height.'" de:peso="'.$as->size.'" de:created="'.$as->created.'"  de:description="'.$as->description.'"  de:tags="'.$as->metadata.'" title="Desc:'.$as->description.' Tags:'.$as->metadata.'" />'.
                                                    '</a></div></li>	';
                                            }else{
                                                 echo '<li><div style="float: left;"> <a>'.
                                                     '<img style="'.smarty_function_cssimagescale($params).'"  de:width="'.smarty_function_cssimagescale($params2).'" src="'.MEDIA_IMG_PATH_WEB.$as->path_file.'140x100-'.$as->name.'" id="draggable_img'.$num.'" class="draggable" name="'.$as->pk_photo.'"  de:path="'.$as->path_file.'" border="0" de:mas="'.$as->name.'" de:ancho="'.$as->width.'" de:alto="'.$as->height.'" de:peso="'.$as->size.'" de:created="'.$as->created.'"  de:description="'.$as->description.'"  de:tags="'.$as->metadata.'" title="Desc:'.$as->description.' Tags:'.$as->metadata.'" />'.
                                                    '</a></div></li>	';
                                            }
                                            $num++;
                                        }
                                    }else{
                                        $ph=new Photo($as->pk_photo);
                                        $ph->set_status(0,$_SESSION['userid']);
                                    }
				 }            
				 echo "</ul><br />";				 
				 exit(0);					
			break;
		
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
}

$tpl->display('album.tpl');
?>
