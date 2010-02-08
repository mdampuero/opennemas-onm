<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gesti&oacute;n de Opinion');

require_once('core/method_cache_manager.class.php');

require_once('core/pc_content_category_manager.class.php');
require_once('core/pc_content_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_opinion.class.php');
require_once('core/pc_user.class.php');

 $cc = new PC_ContentCategoryManager();

$allcategorys = $cc->find_by_type('4', 'inmenu=1 AND fk_content_type=4 and available=1','ORDER BY posmenu');
$tpl->assign('allcategorys', $allcategorys);


if (!isset($_GET['category']) || empty($_GET['category'])) {$_GET['category'] = 6;} //Opinion
if (!isset($_GET['page']) || empty($_GET['page'])) {$_GET['page'] = 1;}
if($_REQUEST['action']!='list' ) { $_GET['msg']=''; $msg='';}

$tpl->assign('category', $_GET['category']);

//hemeroteca content_status=0 available=? favorite=0
// disponible available=1 content_status=?, favorite=0
//Favorito: available=1 content_status=1, favorite=1

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':  
			
			$cm = new PC_ContentManager();
		
                        list($opinions, $pager)= $cm->find_pages('PC_Opinion', 'content_status=0 ', 'ORDER BY  created DESC ',$_GET['page'],10, $_GET['category']);

                        $users = PC_User::get_instance();
                        $conecta_users = $users->get_all_authors();
                        $tpl->assign('conecta_users', $conecta_users);
                        $tpl->assign('paginacion', $pager);
			$tpl->assign('opinions', $opinions);

			$_SESSION['pc_from']='pc_opinion';
		break;

		case 'new':

                        $tpl->assign('categorys', $allcategorys);

                        $users = PC_User::get_instance();
                        $conecta_users = $users->get_all_authors();
                        $tpl->assign('conecta_users', $conecta_users);

		break;

		case 'read': //habrá que tener en cuenta el tipo
                        $opinion = new PC_Opinion( $_REQUEST['id'] );
                        $tpl->assign('opinion', $opinion);

                        $tpl->assign('categorys', $allcategorys);
                        
                        $users = PC_User::get_instance();
                        $conecta_users = $users->get_all_authors();
                        $tpl->assign('conecta_users', $conecta_users);

		break;

		case 'update':
			$opinion = new PC_Opinion();			
			$opinion->update( $_REQUEST );
			if($_SESSION['pc_from']=='pc_hemeroteca'){			
				Application::forward('pc_hemeroteca.php?action=list&category='.$_REQUEST['category'].'&mytype=pc_opinion');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&mytype=pc_opinion');
			}
		break;

		case 'create':
			$opinion = new PC_Opinion();
			if($opinion->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&mytype=pc_opinion');
			} else {
				$tpl->assign('errors', $opinion->errors);
			}
		break;

		case 'delete':
			$opinion = new PC_Opinion();
			$opinion->delete( $_POST['id'] );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&mytype=pc_opinion');
		break;

		case 'validate':
			$opinion = null;
			if(empty($_POST["id"])) {
				$opinion = new PC_Opinion();
				if(!$opinion->create( $_POST ))
					$tpl->assign('errors', $opinion->errors);		
			} else {
				$opinion = new PC_Opinion();
				$opinion->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$opinion->pk_pc_opinion);
		break;
		
                case 'change_status':
			$opinion = new PC_Opinion($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			if($opinion->favorite == 1 and $status ==1){
				$msg=$opinion->title.' '.' No se puede archivar, es favorito';
			} else{
				$opinion->set_status($status);
			}
			if($_GET['from']=='index'){
				Application::forward('pc_index.php?action=list');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype=pc_opinion&category='.$_GET['category'].'&msg='.$msg);
			}
		break;

		case 'change_available':
			$opinion = new PC_Opinion($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			if($opinion->favorite == 1 and $status ==0){
				$msg=$opinion->title.' '.'No se puede despublicar, es favorito';
			} else{
				$opinion->set_available($status);
			}
			if($_GET['from']=='index'){
				Application::forward('pc_index.php?action=list');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype=pc_opinion&category='.$_GET['category'].'&msg='.$msg);
			}
		break;

		case 'change_favorite':
			$opinion = new PC_Opinion($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                        if($opinion->available==1){
                                $opinion->set_favorite($status);
                        }else{
                                $msg= $opinion->title.' '."No puede ser favorito, está despublicado ";
                        }
			if($_GET['from']=='index'){
				Application::forward('pc_index.php?action=list&msg='.$msg);
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype=pc_opinion&category='.$_GET['category'].'&msg='.$msg);
			}
		break;

		case 'mstatus':
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			     $fields = $_REQUEST['selected_fld'];

		            if(is_array($fields)) {
                                  foreach($fields as $i ) {
                                        $opinion = new PC_Opinion($i);
                                        if($opinion->favorite == 1 and $_REQUEST['id'] ==0){
                                                $msg= $opinion->title.' '. 'No se puede archivar, es favorito';
                                        } else{
                                                 $opinion->set_status($_REQUEST['id']);
                                        }

                                  }
        		    }
			  }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype=pc_opinion&category='.$_REQUEST['category'].'&msg='.$msg);
		break;

                case 'mavailable':
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			     $fields = $_REQUEST['selected_fld'];

		            if(is_array($fields)) {
                                foreach($fields as $i ) {
                                    $opinion = new PC_Opinion($i);
                                     if($opinion->favorite == 1 and $_REQUEST['id'] ==0){
                                                $msg= ''.$opinion->title.' '. 'No se puede despublicar, es favorito';
                                        } else{
                                            $opinion->set_available($_REQUEST['id']);   //Se reutiliza el id para pasar el estatus
                                        }
                                }
        		    }
			  }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype=pc_opinion&category='.$_GET['category'].'&msg='.$msg);
		break;

		case 'mdelete':

                      if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
                      {
                        $fields = $_REQUEST['selected_fld'];

                        if(is_array($fields)) {
                           foreach($fields as $i ) {
                                $opinion = new PC_Opinion($i);
                                $opinion->delete( $i );
                           }
                        }
                      }

                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype=pc_opinion&category='.$_GET['category'].'&msg='.$msg);
		break;
		
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype=pc_opinion&category='.$_GET['category']);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->display('pc_opinion.tpl');
 