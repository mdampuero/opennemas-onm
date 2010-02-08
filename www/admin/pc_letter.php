<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Plan Conecta: Gesti&oacute;n de Cartas al Director');

require_once('core/method_cache_manager.class.php');

require_once('core/pc_content_manager.class.php');
require_once('core/pc_content_category_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_letter.class.php');
require_once('core/pc_user.class.php');


if (!isset($_GET['category']) || empty($_GET['category'])) {$_GET['category'] = 5;} 
if (!isset($_GET['page']) || empty($_GET['page'])) {$_GET['page'] = 1;}
if($_REQUEST['action']!='list' ) { $_GET['msg']=''; $msg='';}

$cc = new PC_ContentCategoryManager();
$allcategorys = $cc->find_by_type('3', 'inmenu=1 AND fk_content_type=3 and available=1','ORDER BY posmenu');
$tpl->assign('allcategorys', $allcategorys);
$tpl->assign('category', $_GET['category']);



//hemeroteca content_status=0 available=? favorite=0
// disponible available=1 content_status=?, favorite=0
//Favorito: available=1 content_status=1, favorite=1

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':  
	
			$cm = new PC_ContentManager();
		 
                        list($letters, $pager)= $cm->find_pages('PC_Letter', 'content_status=0 ', 'ORDER BY  created DESC ',$_GET['page'],10, $_GET['category']);

                        $users = PC_User::get_instance();
                        $conecta_users = $users->get_all_authors();
                        $tpl->assign('conecta_users', $conecta_users);
                        $tpl->assign('paginacion', $pager);
			$tpl->assign('letters', $letters);	
			$_SESSION['pc_from']='pc_letter'; //Para regresar desde las botoneras.
		break;

		case 'new':
			// Nada

			 	 $tpl->assign('categorys', $allcategorys);
				//Autores combobox.
			 	$users = PC_User::get_instance();
                                $conecta_users = $users->get_all_authors();
                                $tpl->assign('conecta_users', $conecta_users);
		break;

		case 'read': //habrá que tener en cuenta el tipo
				$letter = new PC_Letter( $_REQUEST['id'] );				
				$tpl->assign('letter', $letter);
			 	$tpl->assign('categorys', $allcategorys);
			 	//Autores combobox.
				$users = PC_User::get_instance();
                                $conecta_users = $users->get_all_authors();
                                $tpl->assign('conecta_users', $conecta_users);
		break;
                
                case 'create':
			$letter = new PC_Letter();
			if($letter->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
			} else {
				$tpl->assign('errors', $letter->errors);
			}
		break;

		case 'update':
			$letter = new PC_Letter();			
			$letter->update( $_REQUEST );
			if($_SESSION['pc_from']=='pc_hemeroteca'){			
				Application::forward('pc_hemeroteca.php?action=list&category='.$_REQUEST['category'].'&mytype=pc_letter');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&mytype=pc_letter');
			}
		break;

                case 'delete':
			$letter = new PC_Letter();
			$letter->delete( $_POST['id'] );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&mytype=pc_letter');
		break;
                
		case 'validate':
			$letter = null;
			if(empty($_POST["id"])) {
				$letter = new PC_Letter();
				if(!$letter->create( $_POST ))
					$tpl->assign('errors', $user->errors);		
			} else {
				$letter = new PC_Letter();
				$letter->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$letter->pk_pc_letter);
		break;
		
		case 'change_status':
			$letter = new PC_Letter($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			if($letter->favorite == 1 and $status ==1){
				$msg=$letter->title.' '.' No se puede archivar, es favorito';
			} else{
				$letter->set_status($status);
			}
			if($_GET['from']=='index'){
				Application::forward('pc_index.php?action=list');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&mytype=pc_letter&msg='.$msg);
			}
		break;

		case 'change_available':
			$letter = new PC_Letter($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			if($letter->favorite == 1 and $status ==0){
				$msg=$letter->title.' '.'No se puede despublicar, es favorito';
			} else{
				$letter->set_available($status);
			}
			if($_GET['from']=='index'){
				Application::forward('pc_index.php?action=list');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&&category='.$_REQUEST['category'].'&mytype=pc_letter&msg='.$msg);
			}
		break;

		case 'change_favorite':
			$letter = new PC_Letter($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                        if($letter->available==1){
                                $letter->set_favorite($status);
                        }else{
                                $msg= $letter->title.' '."No puede ser favorito, está despublicado ";
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
                                        $letter = new PC_Letter($i);
                                        if($letter->favorite == 1 and $_REQUEST['id'] ==0){
                                                $msg= $letter->title.' '. 'No se puede archivar, es favorito';
                                        } else{
                                                 $letter->set_status($_REQUEST['id']);
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
                                    $letter = new PC_Letter($i);
                                     if($letter->favorite == 1 and $_REQUEST['id'] ==0){
                                                $msg= ''.$letter->title.' '. 'No se puede despublicar, es favorito';
                                        } else{
                                            $letter->set_available($_REQUEST['id']);   //Se reutiliza el id para pasar el estatus
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
                                $letter = new PC_Letter($i);
                                $letter->delete( $i );
                           }
                        }
                      }

                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_GET['category'].'&msg='.$msg);
		break;
		
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&mytype=pc_letter');
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->display('pc_letter.tpl');
?>
