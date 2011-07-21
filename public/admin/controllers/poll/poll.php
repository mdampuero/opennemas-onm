<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');
 
\Onm\Module\ModuleManager::checkActivatedOrForward('POLL_MANAGER');

Acl::checkOrForward('POLL_ADMIN');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Polls Management');

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT, array('options' => array('default' => 1)));


/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('poll');

 
$category = filter_input(INPUT_GET,'category');
if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT, array('options' => array('default' => 0 )));
}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);
if(empty($category))
    $category = $categoryData[0]->pk_content_category;
$tpl->assign('category', $category);
$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $categoryData);
$allcategorys = $parentCategories;
 
if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':  //Buscar publicidad entre los content

            $cm = new ContentManager();
		     	// ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
			list($polls, $pager)= $cm->find_pages('Poll', 'fk_content_type=11 ', 'ORDER BY  created DESC ',$page,10, $category);

			foreach($polls as $poll){
                $poll->category_name = $poll->loadCategoryName($poll->pk_content);

			}

			/* Ponemos en la plantilla la referencia al objeto pager */
			$tpl->assign('paginacion', $pager);
			$tpl->assign('polls', $polls);


			break;

		case 'new':
            Acl::checkOrForward('POLL_CREATE');
			// Nada
			break;

		case 'read': //habrá que tener en cuenta el tipo
             Acl::checkOrForward('POLL_UPDATE');
			$poll = new Poll( $_REQUEST['id'] );
			$tpl->assign('poll', $poll);

			$items=$poll->get_items($_REQUEST['id']);
			$tpl->assign('items', $items);
		break;

		case 'create':
             Acl::checkOrForward('POLL_CREATE');
			$poll = new Poll();
			$_POST['publisher'] = $_SESSION['userid'];
			if($poll->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
			} else {
				$tpl->assign('errors', $poll->errors);
			}
		break;

		case 'update':
             Acl::checkOrForward('POLL_UPDATE');
			$poll = new Poll();
			$poll->update( $_REQUEST );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

		break;

		case 'validate':
			$poll = null;

			if(empty($_POST["id"])) {
                 Acl::checkOrForward('POLL_CREATE');
				$poll = new Poll();
				if(!$poll->create( $_POST ))
					$tpl->assign('errors', $poll->errors);
			} else {
                 Acl::checkOrForward('POLL_UPDATE');
				$poll = new Poll();
				$poll->update( $_REQUEST );
			}

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$poll->id);
		break;

		case 'delete':
            Acl::checkOrForward('POLL_DELETE');
			$poll = new Poll();
			$poll->delete( $_POST['id'] );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
		break;

		case 'change_status':
             Acl::checkOrForward('POLL_AVAILABLE');
			$poll = new Poll($_REQUEST['id']);
			//Publicar o no,
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			$poll->set_available($status, $_SESSION['userid']);

			if($_GET['from']=='index'){
				Application::forward('index.php?action=list');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
			}
		break;

		case 'change_favorite':
             Acl::checkOrForward('POLL_FAVORITE');
            $poll = new Poll($_REQUEST['id']);
            //Publicar o no,
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $poll->set_favorite($status);
			if($_GET['from']=='index'){
				Application::forward('index.php?action=list&msg='.$msg);
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
			}
		break;

		case 'change_available':
            Acl::checkOrForward('POLL_AVAILABLE');

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
             Acl::checkOrForward('POLL_AVAILABLE');
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

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
		break;
		case 'mdelete':
              Acl::checkOrForward('POLL_DELETE');
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

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
		break;
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
}


$tpl->display('polls/poll.tpl');
