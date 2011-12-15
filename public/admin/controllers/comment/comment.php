<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message as m;

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

// Check MODULE
\Onm\Module\ModuleManager::checkActivatedOrForward('COMMENT_MANAGER');
// Check ACL
Acl::checkOrForward('COMMENT_ADMIN');


/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Comment Management');



// Initialize request parameters
$page = filter_input ( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 0)) );
//Al borrar un comentario, $_POST['action'] nunca se asignaba a $action
if(isset($_POST['action'])
        && ($_POST['action'] == 'delete'
         || $_POST['action'] == 'mdelete'
         || $_POST['action'] == 'mfrontpage'
         || $_POST['action'] == 'read'
         || $_POST['action'] == 'update'))
{
    $action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}else{
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}
$category = filter_input( INPUT_GET, 'category' , FILTER_SANITIZE_STRING );
if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT, array('options' => array('default' => 'todos' )));
}

$module = filter_input(INPUT_GET,'module',FILTER_VALIDATE_INT, array('options' => array('default' => 0 )));

$tpl->assign('category', $category);

function buildFilter($filter) {
    $filters = array();
    $url = array();

    $filters[] = $filter;

    if(isset($category)) {
        $url[] = 'category=' . $category;
    }

    if(isset($module)
       && ($module > 0)) {
        $url[] = 'module=' . $module;
        //$filters[] = '`fk_content_type`=' . $module;
    }

    return array( implode(' AND ',$filters), implode('&amp;', $url) );
}


switch($action) {

    case 'list': 

        // Get all categories for the menu
        $ccm = ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($category,$module);
        $tpl->assign('subcat', $subcat);
        $tpl->assign('allcategorys', $parentCategories);
        $tpl->assign('datos_cat', $datos_cat);

        // Set up the list of content types that could have comments
        $content_types = array(1 => _('Article') , 7 => _('Album'), 9 => _('Video'), 4 => _('Opinion'), 11 => _('Poll'));
        $tpl->assign('content_types', $content_types);

        $cm = new ContentManager();
        $commentStatus = filter_input ( INPUT_GET, 'comment_status' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 0)) );

        $tpl->assign('comment_status', $commentStatus);
        $filter = "content_status = ".$commentStatus;

     
        if($category == 'home') {
            $comment = new Comment();

            //Comentarios de las noticias in_home
            $comments = $comment->get_home_comments($filter);
            if($comments) {
                $comments = $cm->paginate_num($comments,ITEMS_PAGE);
                $tpl->assign('paginacion', $cm->pager);
                $tpl->assign('comments', $comments);
            }

        } elseif($category == 'todos') {
            $comment = new Comment();
            // ContentManager::find_pages(<TIPO>, <WHERE>, <ORDER>, <PAGE>, <ITEMS_PER_PAGE>, <CATEGORY>);
            list($allComments, $pager)= $cm->find_pages('Comment', $filter.' ',
                                                     'ORDER BY  created DESC ',
                                                     $page, ITEMS_PAGE);
            $comments = array();
            if ($module != 0) {
                foreach ($allComments as $comm) {
                    $comm->content_type = ContentType::getContentTypeByContentId($comm->fk_content);

                    if ($comm->content_type == $module) {
                        $comments[] = $comm;
                    }                
                } 
            } else {
                $comments = $allComments;
            }
            
            

            $tpl->assign('paginacion', $pager);
            $tpl->assign('comments', $comments);


        } else {
            // ContentManager::find_pages(<TIPO>, <WHERE>, <ORDER>, <PAGE>, <ITEMS_PER_PAGE>, <CATEGORY>);
            list($allComments, $pager) = $cm->find_pages('Comment', ' fk_content_type=6  and '.$filter.' ',
                                                      'ORDER BY content_status, created DESC ',
                                                      $page, ITEMS_PAGE, $category);
            $comments = array();
            if ($module != 0) {
                foreach ($allComments as $comm) {
                    $comm->content_type = ContentType::getContentTypeByContentId($comm->fk_content);

                    if ($comm->content_type == $module) {
                        $comments[] = $comm;
                    }                
                } 
            } else {
                $comments = $allComments;
            }
            
            $tpl->assign('paginacion', $pager);
            $tpl->assign('comments', $comments);

        }

        $articles = array();
        $votes = array();
        $i = 0;  //Sacamos los articulos para el titulo
        if($comments) {
            // sql sobre article y content * IN ($prima->fk_content1, $prima->fk_content2, ...)
            $ids = array();
           /* foreach($comments as $prima) {
                $ids[] = $prima->fk_content;
            }
            */
            foreach($comments as $prima){
                $articles[$i] = new Content( $prima->fk_content );
                $cat=$articles[$i]->loadCategoryName($prima->fk_content );
                if (!$cat) {
                    $articles[$i]->category_name = 'Opinion';
                }
                $articles[$i]->category_name = $articles[$i]->loadCategoryName($prima->fk_content);
                $votes[$i] =new Vote( $prima->pk_comment );
                 $i++;
            }
        }
        $tpl->assign('articles', $articles);
        $tpl->assign('votes', $votes);
        $tpl->display('comment/list.tpl');

         
    break;

    case 'new': {
        Acl::checkOrForward('COMMENT_CREATE');
        
    } break;

    case 'read': 

        Acl::checkOrForward('COMMENT_UPDATE');
        $contentID = filter_input ( INPUT_POST, 'id' , FILTER_SANITIZE_NUMBER_INT);

        // habrá que tener en cuenta el tipo
        $comment = new Comment( $contentID );
        if(!is_null($comment->pk_comment)) {           

            $tpl->assign('comment', $comment);

            $article = new Content( $comment->fk_content );
            $tpl->assign('article', $article);

           
        }
        $tpl->display('comment/read.tpl');

    break;

    case 'update': 
        
        Acl::checkOrForward('COMMENT_UPDATE');
        $comment = new Comment($_REQUEST['id']);

        if(!is_null($comment->pk_comment)) {
            $comment->update( $_REQUEST );
            if($_REQUEST['content_status'] == 1) {
                $article = new Article($comment->fk_content);
                //Para que cambie la fecha changed.
                $article->set_status($article->content_status, $article->fk_user_last_editor);
            }
        } else {
            $comment = new PC_Comment();
            $comment->update( $_REQUEST );
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                             $category . '&page=' . $page);
    break;

    case 'create': 
        Acl::checkOrForward('COMMENT_CREATE');
        $comment = new Comment();
        if($comment->create( $_POST )) {
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                                 $category . '&page=' . $page);
        } else {
            $tpl->assign('errors', $comment->errors);
        }
    break;

    case 'delete':  

        Acl::checkOrForward('COMMENT_DELETE');
        $comment = new Comment();
        $comment->delete($_POST['id'], $_SESSION['userid']);
               
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                             $category . '&page=' . $page /*.'&comment_status='.$_GET['comment_status']*/);
    break;

    case 'change_status': 
        Acl::checkOrForward('COMMENT_AVAILABLE');
        if((isset ($_REQUEST['tipo']) && $_REQUEST['tipo'] == 'encuesta') || ($category == 'encuesta')){
            $comment = new PC_Comment($_REQUEST['id']);
            $comment->set_status($_REQUEST['status'], $_SESSION['userid']);
        } else {
            $comment = new Comment($_REQUEST['id']);

            //Publicar o no,
            if($_REQUEST['status'] == 2) {
                $comment->set_status($_REQUEST['status'], $_SESSION['userid']);
            } else {
                // Ya se cambia en el set_available    $comment->set_status($status,$_SESSION['userid']);
                $comment->set_available($_REQUEST['status'], $_SESSION['userid']);
                $article=new Article($comment->fk_content);
                //Para que cambie la fecha changed.
                $article->set_status($article->content_status, $article->fk_user_last_editor);
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                             $category . '&comment_status=' . $_REQUEST['comment_status'] . '&page=' .
                             $page);
    break;

    case 'mfrontpage': 
        Acl::checkOrForward('COMMENT_AVAILABLE');

        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
            $fields = $_REQUEST['selected_fld'];
            $status = $_REQUEST['id'];

            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $comment = new Comment($i);

                    if(!is_null($comment->pk_comment)) {
                        // Ya se cambia en el set_available  $comment->set_status($status,$_SESSION['userid']);
                        //Se reutiliza el id para pasar el estatus
                        $comment->set_available($status, $_SESSION['userid']);
                        $article = new Article($comment->fk_content);

                        //Para que cambie la fecha changed.
                        $article->set_status($article->content_status, $article->fk_user_last_editor);
                    } else {
                        $comment = new PC_Comment($i);
                        $comment->set_status($status, $_SESSION['userid']);
                    }
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                             $category . '&page=' . $page);
    break;

    case 'mdelete': 
        Acl::checkOrForward('COMMENT_DELETE');
        
        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
            $fields = $_REQUEST['selected_fld'];

            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $comment = new Comment($i);
                    if(!is_null($comment->pk_comment)) {
                        $comment->delete( $i, $_SESSION['userid'] );
                    } else {
                        $comment = new PC_Comment($i);
                        $comment->delete( $i, $_SESSION['userid'] );
                    }
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                             $category . '&comment_status=' . $_REQUEST['comment_status'] . '&page=' . $page);
    break;

    default: 
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&category=' .
                             $category . '&page=' . $page);
    break;
}

