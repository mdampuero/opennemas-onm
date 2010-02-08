<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Gesti&oacute;n de Autores');

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/author.class.php');


// FIXME: revisar
if( !isset($_SESSION['privileges']) || !in_array('USR_ADMIN', $_SESSION['privileges'])) {
    Application::forward('login.php');
}

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':
			$author = new Author();
			$cm = new ContentManager();
			$authors = $author->list_authors(NULL,'order by name');			
			$tpl->assign('authors_list', $authors);										
			$authors = $cm->paginate_num($authors,16);
			$tpl->assign('authors', $authors);							
			$tpl->assign('paginacion', $cm->pager);
		
			$_SESSION['_from']='author.php';	

		break;

		case 'new':	
			//
		break;

		case 'read':
			$author = new Author( $_REQUEST['id'] );		
			$tpl->assign('author', $author);		
			$photos=$author->get_author_photos($_REQUEST['id']);		
	
			$tpl->assign('photos', $photos);
			
		break;

		case 'update':	
			$author = new Author();
			$author->update( $_REQUEST );	
	
			if($_SESSION['_from']=='opinion.php'){
				Application::forward('opinion.php?action=list&page=1');
			}else{
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
			}
		break;

		case 'create':
			$author = new Author();
			if($author->create( $_POST )) {			
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
			} else {
				$tpl->assign('errors', $author->errors);
			}
		break;

		case 'delete':
			$author = new Author();
			$author->delete( $_POST['id'] );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;
		
		case 'validate':

			$author = new Author();
			if(empty($_POST["id"])) {
				
				//Estamos creando un nuevo artículo
				if(!$author->create( $_POST ))
					$tpl->assign('errors', $author->errors);		
			} else {
				$author = new Author($_POST["id"]);
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
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
}

$tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);

$tpl->display('author.tpl');
?>