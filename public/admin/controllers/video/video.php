<?php

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

//Testing Panorama
set_include_path(get_include_path(). PATH_SEPARATOR. SITE_LIBS_PATH.DIRECTORY_SEPARATOR.'Panorama');
require_once(implode(DIRECTORY_SEPARATOR, array('Zend','Gdata','YouTube.php')));
require_once( implode(DIRECTORY_SEPARATOR, array('Panorama','Panorama','Video.php')));

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
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu( $category, $content_types['video']);

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

		case 'list':

			$cm = new ContentManager();
			
            if (empty($page)) {
                $limit= "LIMIT ".(ITEMS_PAGE+1);
            } else {
                $limit= "LIMIT ".($page-1) * ITEMS_PAGE .', '.$numItems;
            }

		    $videos = $cm->find_by_category('Video', $category, 'fk_content_type = 9 ',
                                            'ORDER BY created DESC '.$limit);

            $params = array('page'=>$page, 'items'=>ITEMS_PAGE,
                    'total' => count($videos),
                    'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$category );

            $pagination = Onm\Pager\SimplePager::getPagerUrl($params);
            $tpl->assign('pagination', $pagination);
            $tpl->assign('videos', $videos);

		break;

		case 'new':
			// Nada
 
		break;

		case 'read':
            Acl::checkOrForward('VIDEO_READ');

            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
            if (empty($id)) {
                $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            }
			$video = new Video( $id );

            $tpl->assign('video', $video);

		break;

		case 'create':

            Acl::checkOrForward('VIDEO_CREATE');

            $url = filter_input(INPUT_POST,'video_url',FILTER_VALIDATE_URL);
            if ($url) {
                try {

                    $videoP = new \Panorama\Video($url);
                    $_POST['information'] = $videoP->getVideoDetails();

                    $video = new Video();
                    if($video->create( $_POST )) {
                        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
                    } else {
                        $tpl->assign('errors', $video->errors);
                    }
                } catch (Exception $e) {
                      $tpl->assign('errors', "Can't get video information. Check url");
                }
            }  else {
                $tpl->assign('errors', "Please, Check url");
            }

		break;

		case 'update':

            Acl::checkOrForward('VIDEO_UPDATE');

            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
            $video = new Video($id);
            $url = filter_input(INPUT_POST,'video_url',FILTER_VALIDATE_URL);
            if ($url) {

                if ($video->video_url != $url) {
                    try {
                        $videoP = new \Panorama\Video($url);
                        $_POST['information'] = $videoP->getVideoDetails();

                    } catch (Exception $e) {
                          $tpl->assign('errors', _("Please, check your video url. Seems that is not valid or not supported"));
                    }
                }
                $video->update( $_POST );
            }else {
                $tpl->assign('errors', "Please, insert your video url.");
            }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

		break;

		case 'validate':

			$video = null;
            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
            $video = new Video($id);
            $url = filter_input(INPUT_POST,'video_url',FILTER_VALIDATE_URL);
            if ($url) {

                if ((!$id) || $video->video_url != $url) {
                    try {
                        $videoP = new \Panorama\Video($url);
                        $_POST['information'] = $videoP->getVideoDetails();
                    } catch (Exception $e) {
                          $tpl->assign('errors', "Can't get video information. Check url");
                    }
                }

                if (!$id) {
                    $video = new Video();
                    //Estamos creando un nuevo artículo
                    if(!$video->create( $_POST ))
                        $tpl->assign('errors', $video->errors);
                } else {
                    $video = new Video($id);

                    $_POST['information'] = $video->information;

                     $video->update( $_POST );

                }

                Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$video->id);
            } else {
                $tpl->assign('errors', "Please, Check url");
            }

		break;

        case 'delete':

            Acl::checkOrForward('VIDEO_DELETE');

            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
			$video = new Video($id);
            $rel= new Related_content();
            $relationes=array();
            $msg ='';
            $relationes = $rel->get_content_relations($id);
            if (!empty($relationes)) {
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
            } else {
                $msg.="¿Está seguro que desea eliminar '".$video->title."' ?";
               // $msg.='\n <a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
              //  $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                echo $msg;
                exit(0);
            }

		break;

        case 'yesdel':

            Acl::checkOrForward('VIDEO_DELETE');

            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            if ($id) {
               $video = new Video($_REQUEST['id']);
                //Delete relations
                $rel= new Related_content();
                $rel->delete_all($id);
                $video->delete( $id ,$_SESSION['userid'] );
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);

         break;

		case 'set_position':

			$video = new Video($_REQUEST['id']);
			$video->set_position($_REQUEST['posicion'],$_SESSION['userid']);

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);

		break;

		case 'change_status':

            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
			$video = new Video($id);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			//$video->set_status($status,$_SESSION['userid']);
			$video->set_available($status, $_SESSION['userid']);

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);

		break;

        case 'change_favorite':

            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
			$video = new Video($id);
            $msg = '';
             //Publicar o no,
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            if ($video->available==1) {
                $video->set_favorite($status);
            }else{
                $msg="No se puede esta despublicado";
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$category);

		break;


		case 'mfrontpage':

			if (isset($_REQUEST['selected_fld'])
				&& count($_REQUEST['selected_fld'])>0)
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

			if (isset($_REQUEST['selected_fld'])
				&& count($_REQUEST['selected_fld'])>0)
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
