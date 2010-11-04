<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');
// Register events


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Polls');

require_once(SITE_CORE_PATH.'album_photo.class.php');
require_once(SITE_CORE_PATH.'privileges_check.class.php');

if (!isset($_REQUEST['page'])) {
     $_REQUEST['page'] = 1;
}

if (!isset($_REQUEST['category'])) {
     $_REQUEST['category'] = 15;//deportes
}



$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

// <editor-fold defaultstate="collapsed" desc="Container gente-fotoactualidad">
// Parse template.conf to assign
$tplFrontend = new Template(TEMPLATE_USER);
$section = $ccm->get_name($_REQUEST['category']);
$section = (empty($section))? 'home': $section;

$container_noticias_gente = $tplFrontend->readKeyConfig('template.conf', 'container_noticias_gente', $section);

if($container_noticias_gente == '1') {
    $tpl->assign('bloqueGente', 'GENTE / FOTO ACTUALIDAD');
} else {
    $tpl->assign('bloqueGente', 'FOTO ACTUALIDAD / GENTE');
}
// </editor-fold>

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);
$allcategorys = $parentCategories;
$tpl->assign('category', $_REQUEST['category']);
/*if( !Privileges_check::CheckPrivileges('MUL_ADMIN'))
{
    Privileges_check::AccessDeniedAction();
} 
*/

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':  //Buscar publicidad entre los content
			 
            $cm = new ContentManager();
		     	// ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
			list($polls, $pager)= $cm->find_pages('Poll', 'fk_content_type=11 ', 'ORDER BY  created DESC ',$_REQUEST['page'],10, $_REQUEST['category']);

			foreach($polls as $poll){
                $poll->category_name = $poll->loadCategoryName($poll->pk_content);
 
			}

			/* Ponemos en la plantilla la referencia al objeto pager */
			$tpl->assign('paginacion', $pager);
			$tpl->assign('polls', $polls);


			break;

		case 'new':
			// Nada
			break;

		case 'read': //habrá que tener en cuenta el tipo
			$poll = new Poll( $_REQUEST['id'] );
			$tpl->assign('poll', $poll);

			$items=$poll->get_items($_REQUEST['id']);
			$tpl->assign('items', $items);
		break;

		case 'create':
			$poll = new Poll();
			$_POST['publisher'] = $_SESSION['userid'];
			if($poll->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
			} else {
				$tpl->assign('errors', $poll->errors);
			}
		break;

		case 'update':
			$poll = new Poll();
			$poll->update( $_REQUEST );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);

		break;

		case 'validate':
			$poll = null;
            
			if(empty($_POST["id"])) {
				$poll = new Poll();
				if(!$poll->create( $_POST ))
					$tpl->assign('errors', $poll->errors);
			} else {
				$poll = new Poll();
				$poll->update( $_REQUEST );
			}
            
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$poll->id);
		break;

		case 'delete':
			$poll = new Poll();
			$poll->delete( $_POST['id'] );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;

		case 'change_status':
			$poll = new Poll($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			$poll->set_available($status, $_SESSION['userid']);

			if($_GET['from']=='index'){
				Application::forward('index.php?action=list');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
			}
		break;

		case 'set_view_column':
            $cm = new ContentManager();
             $polls = $cm->find('Poll', 'content_status=1 and view_column=1', 'ORDER BY created DESC');
            if((count($polls)<2) OR $_REQUEST['status']!=1){
                $poll = new Poll($_REQUEST['id']);
                //Publicar o no,
                $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                $poll->set_view_column($status);
             }else{
                $msg="Ya hay dos encuestas en portada.";
            }
			if($_GET['from']=='index'){
				Application::forward('index.php?action=list&msg='.$msg);
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$_REQUEST['category']);
			}
		break;

		case 'change_favorite':

                        $poll = new Poll($_REQUEST['id']);
                        //Publicar o no,
                        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                        $poll->set_favorite($status);
			if($_GET['from']=='index'){
				Application::forward('index.php?action=list&msg='.$msg);
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
			}
		break;

		case 'change_available':
			$poll = new Poll($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			$poll->set_available($_REQUEST['id'],$status);

			if($_GET['from']=='index'){
				Application::forward('index.php?action=list');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
			}
		break;

		case 'mfrontpage':
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			     $fields = $_REQUEST['selected_fld'];

		            if(is_array($fields)) {
					      foreach($fields as $i ) {
					        $poll = new Poll($i);
					        $poll->set_status($_REQUEST['id']);   //Se reutiliza el id para pasar el estatus
					        }
        		    }
			  }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
		break;
		case 'mdelete':

			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			    $fields = $_REQUEST['selected_fld'];

		            if(is_array($fields)) {
				      foreach($fields as $i ) {
				        $poll = new Poll($i);
						$poll->delete( $i);
						}
        		    }
			  }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
}


$tpl->display('polls/poll.tpl');

