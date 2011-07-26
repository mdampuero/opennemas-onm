<?php

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

//Check if module is activated in this onm instance
 \Onm\Module\ModuleManager::checkActivatedOrForward('VIDEO_MANAGER');

// Check if the user can admin video
Acl::checkOrForward('VIDEO_ADMIN');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Video Management');


//Testing Panorama
set_include_path(get_include_path(). PATH_SEPARATOR. SITE_LIBS_PATH.DIRECTORY_SEPARATOR.'Panorama');
require_once(implode(DIRECTORY_SEPARATOR, array('Zend','Gdata','YouTube.php')));
require_once( implode(DIRECTORY_SEPARATOR, array('Panorama','Panorama','Video.php')));

define('VIDEO_FAVORITES', 4);

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);

/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('video');

$category = filter_input(INPUT_GET,'category',FILTER_SANITIZE_STRING);

if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING);

}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu( $category, $contentType);
if(empty($category)) {$category ='favorite';}
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

            if ($category == 'favorite') { //Widget video
                $videos = $cm->find_all('Video', 'favorite = 1 AND available =1', 'ORDER BY  created DESC '. $limit);
                if (count($videos) != VIDEO_FAVORITES ) {
                   $tpl->assign('msg', _("Should have ".VIDEO_FAVORITES." videos ")) ;
                }
                if(!empty($videos)){
                    foreach ($videos as &$video) {
                        $video->category_name = $ccm->get_name($video->category);
                        $video->category_title = $ccm->get_title($video->category_name);
                    }
                }

            } else {
                $videos = $cm->find_by_category('Video', $category, 'fk_content_type = 9 ',
                                            'ORDER BY created DESC '.$limit);
            }
            $params = array('page'=>$page, 'items'=>ITEMS_PAGE,
                    'total' => count($videos),
                    'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$category );

            $pagination = \Onm\Pager\SimplePager::getPagerUrl($params);
            $tpl->assign( array(
                            'pagination' => $pagination,
                            'videos' => $videos ));

			$tpl->display('video/list.tpl');

		break;

		case 'new':

			$tpl->display('video/new.tpl');

		break;

		case 'read':
            Acl::checkOrForward('VIDEO_UPDATE');

            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
            if (empty($id)) {
                $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            }
			$video = new Video( $id );

            $tpl->assign('information', $video->information);

            $tpl->assign('video', $video);

			$tpl->display('video/new.tpl');

		break;

        case 'getVideoInformation':

            $url = filter_input(INPUT_GET,'url',FILTER_DEFAULT);
            $url = rawurldecode($url);
            if ($url) {
                $fetchedFromAPC = false;
                if (extension_loaded('apc')) {
                    $information = apc_fetch(APC_PREFIX ."video_".$url, $fetchedFromAPC);
                }
                if (!$fetchedFromAPC) {
                    try {

                        $videoP = new \Panorama\Video($url);

                        $information = $videoP->getVideoDetails();
                        
                    } catch (Exception $e) {
                           $html_out = _( "Can't get video information. Check url");
                    }
                }
                $tpl->assign('information', $information);
                $html_out = $tpl->fetch('video/videoInformation.tpl');
                if (extension_loaded('apc')) {
                    apc_store(APC_PREFIX ."video_".$url, $information);
                }
            }  else {
                 $html_out =  _("Please, Check url");
            }
            Application::ajax_out($html_out);


        break;

		case 'create':

            Acl::checkOrForward('VIDEO_CREATE');
            $video = new Video();
            $_POST['information'] = json_decode($_POST['information'], true);
            if($video->create( $_POST )) {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
            } else {
                $tpl->assign('errors', $video->errors);
            }
			$tpl->display('video/new.tpl');

		break;

		case 'update':

            Acl::checkOrForward('VIDEO_UPDATE');

            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
            $video = new Video($id);


            $_POST['information'] = json_decode($_POST['information'], true);

            if(!Acl::check('CONTENT_OTHER_UPDATE') && $video->pk_user != $_SESSION['userid']) {
                $msg ="Only read";
            }
            $video->update( $_POST );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

		break;

		case 'validate':


            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
            $_POST['information'] = json_decode($_POST['information'],true);

            if (!$id) {
                Acl::checkOrForward('VIDEO_CREATE');
                $video = new Video();

                //Estamos creando un nuevo artículo
                if(!$video->create( $_POST ))
                    $tpl->assign('errors', $video->errors);
            } else {
                Acl::checkOrForward('VIDEO_UPDATE');
                $video = new Video($id);
                if(!Acl::check('CONTENT_OTHER_UPDATE') && $video->pk_user != $_SESSION['userid']) {
                    $msg ="Only read";
                }
                $video->update( $_POST );
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$video->id);

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
                 $msg.="  ¡Ojo! Si lo borra, se eliminaran las relaciones del video con los articulos";
                 $msg.="  ¿Desea eliminarlo igualmente?";
                  $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                  $msg.='   <a href="#" onClick="hideMsgContainer(\'messageBoard\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                 echo $msg;
                 exit(0);
            } else {
                 $msg.="¿Está seguro que desea eliminar '".$video->title."' ?";
                 $msg.='  <a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'"><img src="'.SITE_URL_ADMIN.'/themes/default/images/ok.png" alt="SI"> </a> ';
                 $msg.='   <a href="#" onClick="hideMsgContainer(\'messageBoard\');"> <img src="'.SITE_URL_ADMIN.'/themes/default/images/no.png" alt="NO"></a></p>';
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

            Acl::checkOrForward('VIDEO_AVAILABLE');
            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
			$video = new Video($id);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			//$video->set_status($status,$_SESSION['userid']);
			$video->set_available($status, $_SESSION['userid']);

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);

		break;

        case 'change_favorite':

            Acl::checkOrForward('VIDEO_FAVORITE');
            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
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

            Acl::checkOrForward('VIDEO_AVAILABLE');
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

            Acl::checkOrForward('VIDEO_DELETE');
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
