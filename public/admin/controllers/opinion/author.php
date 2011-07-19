<?php

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Author Opinion Management');
 
Acl::checkOrForward('USER_ADMIN');

$_REQUEST['page'] = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;

if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list':
            Acl::checkOrForward('USER_ADMIN');

			$author = new Author();
			$cm = new ContentManager();
			$authors = $author->list_authors(NULL,'order by name asc');
			$tpl->assign('authors_list', $authors);
			$authors = $cm->paginate_num($authors,20);
			$tpl->assign('authors', $authors);
			$tpl->assign('paginacion', $cm->pager);

			$_SESSION['_from']='author.php';

		break;

		case 'new':
        //
		break;

		case 'read':
            Acl::checkOrForward('AUTHOR_UPDATE');
			$author = new Author( $_REQUEST['id'] );
			$tpl->assign('author', $author);
			$photos=$author->get_author_photos($_REQUEST['id']);

			$tpl->assign('photos', $photos);

		break;

		case 'update':
            Acl::checkOrForward('AUTHOR_UPDATE');
			$author = new Author();
			$author->update( $_REQUEST );

			if($_SESSION['_from']=='opinion.php'){
				Application::forward('controllers/opinion/opinion.php?action=list&page=1');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
			}
		break;

		case 'create':
            Acl::checkOrForward('AUTHOR_CREATE');
			$author = new Author();
			if($author->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
			} else {
				$tpl->assign('errors', $author->errors);
			}
		break;

		case 'delete':
            Acl::checkOrForward('AUTHOR_DELETE');
			$author = new Author();
			$author->delete( $_POST['id'] );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;

		case 'validate':
			$author = new Author();
			if($_GET['action'] == 'new') {

                            //Estamos creando un nuevo artículo
                            if(!$author->create( $_POST )) {
                                $tpl->assign('errors', $author->errors);
                            }
			} else {
				$author = new Author($_GET['id']);
				//Estamos atualizando un artículo
				$author->update( $_REQUEST );
			}
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$author->pk_author);
		break;

		case 'check_img_author':
			$ok='no';
			$img=$_REQUEST['id_img'];
			$cm = new ContentManager();
			$opinions = $cm->find('Opinion', 'fk_content_type=4 and (fk_author_img = '.$img.' OR fk_author_img_widget = '.$img.') ' , 'ORDER BY type_opinion DESC');
			if(!empty($opinions)){
				$ok='si';
			}
			Application::ajax_out($ok);
		break;

		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;
	}
} else {
	$page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : 0;
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
}

$tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);

$tpl->display('opinion/author.tpl');
