<?php
/*}
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

use Onm\Settings as s;
use Onm\Message as m;

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Author Opinion Management');

Acl::checkOrForward('AUTHOR_ADMIN');


$page = filter_input(
    INPUT_GET,
    'page',
    FILTER_VALIDATE_INT,
    array('options' => array('default' => '1'))
);


$tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
if (!isset($action)) {
    $action = filter_input(
        INPUT_GET,
        'action',
        FILTER_SANITIZE_STRING,
        array('options' => array('default' => 'list'))
    );
}

switch($action) {
    case 'list':

        Acl::checkOrForward('AUTHOR_ADMIN');

        $cm         = new ContentManager();
        $author     = new Author();
        $authors    = $author->list_authors(null, 'ORDER BY name ASC');
        $authorsPag = $cm->paginate_num($authors, 20);

        $tpl->assign(
            array(
                'authors_list' 	=> $authors,
                'authors'		=> $authorsPag,
                'paginacion'	=> $cm->pager,
            )
        );

        $_SESSION['_from'] = 'author.php';

        $tpl->display('opinion/authors/list.tpl');

        break;
    case 'new':

        $tpl->display('opinion/authors/new.tpl');

        break;
    case 'read':

        Acl::checkOrForward('AUTHOR_UPDATE');

        $id     = filter_input(INPUT_GET, 'id', FILTER_DEFAULT);
        $author = new Author($id);
        $photos = $author->get_author_photos($id);

        $tpl->assign('photos', $photos);
        $tpl->assign('author', $author);

        $tpl->display('opinion/authors/new.tpl');

        break;
    case 'update':
        Acl::checkOrForward('AUTHOR_UPDATE');

        if ( isset($_POST['params']['inrss'] )) {
            $_POST['params']['inrss'] = 1;
        } else {
            $_POST['params']['inrss'] = 0;
        }

        $author = new Author();
        $author->update($_POST);

        if ($_SESSION['_from']=='opinion.php') {
            Application::forward('controllers/opinion/opinion.php?action=list&page=1');
        } else {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
        }

        break;
    case 'create':

        Acl::checkOrForward('AUTHOR_CREATE');

        if (isset($_POST['params']['inrss'])) {
            $_POST['params']['inrss'] = 1;
        } else {
            $_POST['params']['inrss'] = 0;
        }

        $author = new Author();
        if ($author->create($_POST)) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
        } else {
            $tpl->assign('errors', $author->errors);
        }

        $tpl->display('opinion/authors/new.tpl');

        break;
    case 'getOpinions':

        $id       = filter_input(INPUT_GET, 'id', FILTER_DEFAULT);
        $msg      ='';
        $opinion  = new Opinion();
        $opinions = $opinion->getLatestOpinionsForAuthor($id);

        if (!empty($opinions) && count($opinions>0)) {
            $msg = sprintf(_("<br>The author has some published "));

            $msg.="<br> "._("Caution! Are you sure that you want to delete this author and its opinions?");

            echo $msg;
        }

        exit(0);

        break;
    case 'delete':

        Acl::checkOrForward('AUTHOR_DELETE');

        $id       = filter_input(INPUT_POST, 'id', FILTER_DEFAULT);

        $author   = new Author();
        $opinion  = new Opinion();
        $opinions = $opinion->getLatestOpinionsForAuthor($id);

        $author->delete($id);
        if (!empty($opinions) && count($opinions>0)) {
            foreach ($opinions as $op) {
                $opinion->delete($op->pk_opinion, $_SESSION['userid']);
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);

        break;
    case 'validate':

        if (isset($_POST['params']['inrss'])) {
            $_POST['params']['inrss'] = 1;
        } else {
            $_POST['params']['inrss'] = 0;
        }

        $author = new Author();
        if ($_GET['action'] == 'new') {
            if (!$author->create($_POST)) {
                $tpl->assign('errors', $author->errors);
            }
        } else {
            $author->update($_POST);
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$author->pk_author);

        break;
    case 'check_img_author':

        $ok  ='no';
        $img =$_REQUEST['id_img'];
        $cm  = new ContentManager();

        $opinions = $cm->find(
            'Opinion',
            'fk_content_type=4 and (fk_author_img = '.$img.' OR fk_author_img_widget = '.$img.') ',
            'ORDER BY type_opinion DESC'
        );
        if (!empty($opinions)) {
            $ok='si';
        }
        Application::ajaxOut($ok);

        break;
    case 'batchDelete':

        if (isset($_POST['selected_fld']) && count($_POST['selected_fld']) > 0) {
            $fields = $_POST['selected_fld'];
            $alert  = "";
            $msg    ='The authors ';

            if (is_array($fields)) {
                foreach ($fields as $i) {
                    $author   = new Author($i);
                    $opinion  = new Opinion();
                    $opinions = $opinion->getLatestOpinionsForAuthor($i);

                    if (!empty($opinions) && count($opinions>0)) {
                        $alert = 'ok';
                        $msg  .= " \"" . $author->name . "\",    \n";

                    } else {
                        $author->delete($i, $_SESSION['userid']);
                    }
                }
            }
        }
        if (isset($alert) && $alert =='ok') {
            $msg .= " have opinions.  Delete them one by one";
            m::add($msg);
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&page=' . $page);
        break;
    default:

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);

        break;
}

