<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Plan Conecta: Gesti&oacute;n de Fotos');

require_once('core/method_cache_manager.class.php');

require_once('core/pc_content_manager.class.php');
require_once('core/pc_content_category_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_photo.class.php');
require_once('core/pc_user.class.php');

  
if (!isset($_GET['category']) || empty($_GET['category'])) {$_GET['category'] = 1;} //Photo day
if (!isset($_GET['page']) || empty($_GET['page'])) {$_GET['page'] = 1;}

if($_REQUEST['action']!='list' ) { $_GET['msg']=''; $msg='';}

$tpl->assign('category', $_GET['category']);


$cc = new PC_ContentCategoryManager();
        // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
//Mirar categorias y se recorre para sacar subcategorias.
$allcategorys = $cc->find('inmenu=1 AND fk_content_type=1 and available=1', 'ORDER BY posmenu');
 
$tpl->assign('allcategorys', $allcategorys);
 
$datos_cat = $cc->find('pk_content_category='.$_GET['category'], NULL);	
$tpl->assign('datos_cat', $datos_cat);

 //hemeroteca content_status=0 available=? favorite=0
// disponible available=1 content_status=?, favorite=0
//Favorito: available=1 content_status=1, favorite=1

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		
		case 'list':  

			$cm = new PC_ContentManager();
			list($photos, $pager)= $cm->find_pages('PC_Photo', 'content_status=0 ', 'ORDER BY  created DESC ',$_GET['page'],10, $_GET['category']);

                        $users = PC_User::get_instance();
                        $conecta_users = $users->get_all_authors();
                        $tpl->assign('conecta_users', $conecta_users);
 
			$tpl->assign('paginacion', $pager);
			$tpl->assign('photos', $photos);

			$_SESSION['pc_from']='pc_photo';			
				
		break;

		case 'new':
			// Nada			
                     //   $categorys = $cc->find_by_type('1', 'inmenu=1 and available=1','ORDER BY posmenu');
                        $tpl->assign('categorys', $allcategorys);

                        $users = PC_User::get_instance();
                        $conecta_users = $users->get_all_authors();
                        $tpl->assign('conecta_users', $conecta_users);
				 
		break;

		case 'read': //habrá que tener en cuenta el tipo
                     //   $categorys = $cc->find_by_type('1', 'inmenu=1 and available=1','ORDER BY posmenu');
                        $tpl->assign('categorys', $allcategorys);

                        $photo = new PC_Photo( $_REQUEST['id'] );
                        $tpl->assign('photo', $photo);

                        $users = PC_User::get_instance();
                        $conecta_users = $users->get_all_authors();
                        $tpl->assign('conecta_users', $conecta_users);
                        

		break;

		case 'update':
			$photo = new PC_Photo();			
			$photo->update( $_REQUEST );			
			if($_SESSION['pc_from']=='pc_hemeroteca'){			
				Application::forward('pc_hemeroteca.php?action=list&category='.$_REQUEST['category'].'&mytype=pc_photo');
			}else{			
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
			}
		break;

		case 'create':
		
			$photo = new PC_Photo();
			if($photo->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
			} else {
				$tpl->assign('errors', $photo->errors);
			}
		break;
		
		case 'delete':
			$photo = new PC_Photo();
			$photo->delete( $_POST['id'] );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_GET['category']);
		break;

                case 'validate':
			$photo = null;
			if(empty($_POST["id"])) {
				$photo = new PC_Photo();
				if(!$photo->create( $_POST ))
				   $tpl->assign('errors', $photo->errors);
			} else {
				$photo = new PC_Photo();
				$photo->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$photo->pk_pc_photo);
		break;

		case 'change_status':
			$photo = new PC_Photo($_REQUEST['id']);
			//Publicar o no, 
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			if($photo->favorite == 1 and $status ==1){
				$msg=$photo->title.' '.' No se puede archivar, es favorito';
			} else{
				$photo->set_status($status);
			}
			if($_GET['from']=='index'){
				Application::forward('pc_index.php?action=list&category='.$_GET['category'].'&msg='.$msg);
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_GET['category'].'&msg='.$msg);
			}
		break;
		
		case 'change_available':
			$photo = new PC_Photo($_REQUEST['id']);
			//Publicar o no, 
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			if($photo->favorite == 1 and $status ==0){
				$msg=$photo->title.' '.'No se puede despublicar, es favorito';
			} else{
				$photo->set_available($status);
			}
			if($_GET['from']=='index'){
				Application::forward('pc_index.php?action=list&category='.$_GET['category'].'&msg='.$msg);
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_GET['category'].'&msg='.$msg);
			}
		break;
		
		case 'change_favorite':
			$photo = new PC_Photo($_REQUEST['id']);
			//Publicar o no, 
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                        if($photo->available==1){
                                $photo->set_favorite($status);
                        }else{
                                $msg= $photo->title.' '."No puede ser favorito, está despublicado ";
                        }
			if($_GET['from']=='index'){
				Application::forward('pc_index.php?action=list&msg='.$msg);
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_GET['category'].'&msg='.$msg);
			}
		break;
	
		case 'mstatus':
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			     $fields = $_REQUEST['selected_fld'];

		            if(is_array($fields)) {
                                  foreach($fields as $i ) {
                                        $photo = new PC_Photo($i);
                                        if($photo->favorite == 1 and $_REQUEST['id'] ==0){
                                                $msg= $photo->title.' '. 'No se puede archivar, es favorito';
                                        } else{
                                                 $photo->set_status($_REQUEST['id']);
                                        }

                                  }
        		    }
			  }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&msg='.$msg);
		break;

                case 'mavailable':
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			     $fields = $_REQUEST['selected_fld'];

		            if(is_array($fields)) {
                                foreach($fields as $i ) {
                                    $photo = new PC_Photo($i);
                                     if($photo->favorite == 1 and $_REQUEST['id'] ==0){
                                                $msg= ''.$photo->title.' '. 'No se puede despublicar, es favorito';
                                        } else{
                                            $photo->set_available($_REQUEST['id']);   //Se reutiliza el id para pasar el estatus
                                        }
                                }
        		    }
			  }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_GET['category'].'&msg='.$msg);
		break;
		
		case 'mdelete':
		
                      if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
                      {
                        $fields = $_REQUEST['selected_fld'];

                        if(is_array($fields)) {
                           foreach($fields as $i ) {
                                $photo = new PC_Photo($i);
                                $photo->delete( $i );
                           }
                        }
                      }

                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_GET['category'].'&msg='.$msg);
		break;
		
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->assign('MEDIA_CONECTA_WEB', MEDIA_CONECTA_WEB);
$tpl->display('pc_photo.tpl');
	
