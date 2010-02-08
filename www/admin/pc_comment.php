<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Gesti&oacute;n de Comentarios - Plan Conecta');

require_once('core/method_cache_manager.class.php');


require_once('core/pc_content_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_comment.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_content_category_manager.class.php');
require_once('core/pc_poll.class.php');


if (!isset($_GET['page']) || empty($_GET['page'])) {$_GET['page'] = 1;}
if (!isset($_REQUEST['category'])) {
	$_REQUEST['category'] = '7';
}
$tpl->assign('category', $_REQUEST['category']);


if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
	
    	case 'list':
           
            $cm = new PC_ContentManager();
            if (!isset($_REQUEST['comment_status'])) {
                $_REQUEST['comment_status'] = 0;
            }
            $tpl->assign('comment_status', $_REQUEST['comment_status']);
            $filter="content_status = ".$_REQUEST['comment_status'];
            $comment = new PC_Comment();
                if($_REQUEST['category']=='todos') {                 
                //    $comments = $cm->find('PC_Comment', 'content_status <> 1', 'ORDER BY  created DESC ');
                    list($comments, $pager)= $cm->find_pages('PC_Comment', 'content_status<>1 ', 'ORDER BY  created DESC ',$_GET['page'],10);

                    $tpl->assign('paginacion', $pager);
                    $tpl->assign('comments', $comments);
                }else{
                  //  $comments = $cm->find('PC_Comment', '  '.$filter.' ', 'ORDER BY content_status, created DESC ');
                     list($comments, $pager)= $cm->find_pages('PC_Comment',' '.$filter.' ', 'ORDER BY  created DESC ',$_GET['page'],10);
                    //$comments = $cm->paginate($comments);
                    $tpl->assign('paginacion', $pager);
                    $tpl->assign('comments', $comments);

                }
            

            $polls = array();
            $i=0;	 //Sacamos los articulos para el titulo
            if($comments){
                // sql sobre article y content * IN ($prima->fk_content1, $prima->fk_content2, ...)
                $ids =array();
               
                foreach($comments as $prima){
                    $ids[] = $prima->fk_pc_content;
                }
                //$articles = ContentManager::getContents($ids);
                foreach( $comments as $prima){
                        $polls[$i] = new PC_Poll( $prima->fk_pc_content );
                         $i++;
                }
            }
            $tpl->assign('contents', $polls);
        break;

        case 'new':
                // Nada
        break;

        case 'read': //habrá que tener en cuenta el tipo
                $comment = new PC_Comment( $_REQUEST['id'] );
                $tpl->assign('comment', $comment);
                $poll = new PC_Poll( $comment->fk_pc_content );
                $tpl->assign('article', $poll);
        break;

        case 'update':
                $comment = new PC_Comment();
                $comment->update( $_REQUEST );

                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'create':
                $comment = new PC_Comment();
                if($comment->create( $_POST )) {
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
                } else {
                    $tpl->assign('errors', $comment->errors);
                }
        break;

        case 'delete':
                $comment = new PC_Comment();
                $comment->delete( $_POST['id'] );

                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'change_status':
                $comment = new PC_Comment($_REQUEST['id']);
                //Publicar1 o rechazar2
                $comment->set_status($_REQUEST['status'], $_SESSION['userid']);
               
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&comment_status='.$_REQUEST['comment_status'].'&page='.$_REQUEST['page']);
        break;
      
	//Multiples despublicar y eliminar
        case 'mfrontpage':
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                $fields = $_REQUEST['selected_fld'];
                $status = ($_REQUEST['id']==1)? 1: 0;
                    if(is_array($fields)) {
                        foreach($fields as $i ) {
                              $comment = new PC_Comment($i);
                            // Ya se cambia en el set_available  $comment->set_status($status,$_SESSION['userid']);   //Se reutiliza el id para pasar el estatus
                              $comment->set_available($status, $_SESSION['userid']);

                        }
                    }
              }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'mdelete':
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
                $fields = $_REQUEST['selected_fld'];

                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $comment = new PC_Comment($i);
                        $comment->delete( $i );
                    }
                }
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        default:
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
}

$tpl->display('pc_comment.tpl');
