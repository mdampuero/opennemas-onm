<?php

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

//Check if module is activated in this onm instance
 \Onm\Module\ModuleManager::checkActivatedOrForward('ALBUM_MANAGER');

 // Check if the user can admin album
Acl::checkOrForward('ALBUM_ADMIN');

// Register events
require_once('./albums_events.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', _('Photo Album'));

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);

/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('album');

$category = filter_input(INPUT_GET,'category',FILTER_VALIDATE_INT);
if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT);
}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);
if(empty($category)) {$category ='favorite';}
$tpl->assign('category', $category);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
 
$tpl->assign('datos_cat', $categoryData);

 define('ALBUM_FAVORITES', 4);
/******************* GESTION CATEGORIAS  *****************************/

if( isset($_REQUEST['action']) ) {
    
	switch($_REQUEST['action']) {
		case 'list':  //Buscar publicidad entre los content
            Acl::checkOrForward('ALBUM_ADMIN');
            $cm = new ContentManager();

            if (empty($page)) {
                $limit= "LIMIT ".(ITEMS_PAGE+1);
            } else {
                $limit= "LIMIT ".($page-1) * ITEMS_PAGE .', '.$numItems;
            }

            if ($category == 'favorite') {
                $albums = $cm->find_all('Album', 'favorite =1 AND available =1', 'ORDER BY  created DESC '.$limit);
                if (count($albums) != ALBUM_FAVORITES ) {
                   $tpl->assign('msg', _("Should have ".ALBUM_FAVORITES." ALBUMS ")) ;
                }
                if(!empty($albums)) {
                    foreach ($albums as &$album) {
                        $album->category_name = $ccm->get_name($album->category);
                        $album->category_title = $ccm->get_title($album->category_name);
                    }
                }
                
            } else {
                $albums= $cm->find_by_category('Album', $category, 'fk_content_type=7',
                                               'ORDER BY created '.$limit);
            }

            $params = array('page'=>$page, 'items'=>ITEMS_PAGE,
                    'total' => count($albums),
                    'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$category );

            $pagination = \Onm\Pager\SimplePager::getPagerUrl($params);

            $tpl->assign( array(
                            'pagination' => $pagination,
                            'albums' => $albums ));
            
		break;

		case 'new':
            
		break;

		case 'read':
            
            Acl::checkOrForward('ALBUM_UPDATE');
            
            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
            if(empty($id)) { //because forwards
                $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            }

			$album = new Album( $id);
			$tpl->assign('album', $album);

            $cropExist = file_exists(MEDIA_IMG_PATH_WEB.$album->cover);
            $tpl->assign('crop_exist', $cropExist);

            $photoData = array();
  		 	$photos = $album->get_album($id);
  		 	$tpl->assign('otherPhotos', $photos);
            if (!empty($photos)) {
                foreach ($photos as $ph) {
                    $photoData[] = new Photo($ph[0]);
                }
            }
            $tpl->assign('category', $album->category);
  		 	$tpl->assign('photoData', $photoData);
  		 	
		break;

		case 'update':
            Acl::checkOrForward('ALBUM_UPDATE');

            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
			$album = new Album($id);
            if(!Acl::check('CONTENT_OTHER_UPDATE') && $album->fk_user != $_SESSION['userid']) {
                $msg ="Only read";
            }
			$album->update( $_POST );
                       
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
            
		break;

		case 'create':
            
            Acl::checkOrForward('ALBUM_CREATE');

			$album = new Album();
			if($album->create( $_POST )) {                
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
			}else{
				$tpl->assign('errors', $album->errors);
			}
		break;

        case 'validate':

            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
			if(empty($id)) {
                Acl::checkOrForward('ALBUM_CREATE');
				$album = new Album();
				if(!$album->create( $_POST ))
					$tpl->assign('errors', $album->errors);
			} else {                
                Acl::checkOrForward('ALBUM_UPDATE');
				$album = new Album($id);
                if(!Acl::check('CONTENT_OTHER_UPDATE') && $album->fk_user != $_SESSION['userid']) {
                    $msg ="Only read";
                }
				$album->update( $_POST );
			}

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$album->id.'&category='.$category.'&page='.$page);
		break;

		case 'delete':

            Acl::checkOrForward('ALBUM_DELETE');

            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            
			$album = new Album($id);
            $rel= new Related_content();
            $relations=array();
            $msg ='';
            $relations = $rel->get_content_relations( $id ); 
            if (!empty($relations)) {
                 $msg = "El album  '".$album->title."' , está relacionado con los siguientes articulos:  \n";
                 $cm= new ContentManager();
                 $relat = $cm->getContents($relations);
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
            
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
		break;

        case 'yesdel':
            Acl::checkOrForward('ALBUM_DELETE');

            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            if($id){
                $album = new Album($id);
                //Delete relations
                $rel= new Related_content();
                $rel->delete_all($id);
                $album->delete($id,$_SESSION['userid'] );
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$album->category.'&page='.$_REQUEST['page']);
        break;

		
		case 'change_status':
            Acl::checkOrForward('ALBUM_AVAILABLE');
            
            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            $status = filter_input(INPUT_GET,'status',FILTER_VALIDATE_INT,
                                   array('options' => array('default'=> 0)));
			$album = new Album($id); 
            $album->set_available($status, $_SESSION['userid']);

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

		break;

        case 'change_favorite':

            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            $status = filter_input(INPUT_GET,'status',FILTER_VALIDATE_INT,
                                   array('options' => array('default'=> 0)));
            $album = new Album($id);
            $msg = '';
            if($album->available == 1){
                    $album->set_favorite($status);
            }else{
                    $msg = "No se puede esta despublicado";
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$category);
            
		break;

		case 'mfrontpage':

             Acl::checkOrForward('ALBUM_AVAILABLE');
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
            {
                $fields = $_REQUEST['selected_fld'];
                $status = ($_REQUEST['id']==1)? 1: 0; //Se reutiliza el id para pasar el estatus
                if(is_array($fields))
                {
                    foreach ($fields as $i)
                    {
                        $album = new Album($i);
                        $album->set_available($status, $_SESSION['userid']);
                    }
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
		break;
		
		case 'mdelete':
            Acl::checkOrForward('ALBUM_TRASH');
			if (isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
			    $fields = $_REQUEST['selected_fld'];
                if (is_array($fields)) {
                    $msg="Los albumes ";
                    $alert='';
                    foreach ($fields as $i ) {
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

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&alert='.$alert.'&msgdel='.$msg.'&page='.$page);
		break;
						
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
}

$tpl->display('album/album.tpl');

