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
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

//Check if module is activated in this onm instance
\Onm\Module\ModuleManager::checkActivatedOrForward('BOOK_MANAGER');

 // Check if the user can admin books
Acl::checkOrForward('BOOK_ADMIN');


$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);

/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('book');

$category = filter_input(INPUT_GET,'category',FILTER_VALIDATE_INT);

if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT);
}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);

$bookCategories = array();
foreach($parentCategories as $bCat){
    if($bCat->internal_category == $contentType){
        $bookCategories[] = $bCat;
    }

}
if(empty($category)) {
    $category ='favorite';
}

$tpl->assign('category', $category);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $bookCategories);

$tpl->assign('datos_cat', $categoryData);

/******************* GESTION CATEGORIAS  *****************************/

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

$configurations = s::get('book_settings');
$numFavorites = (isset($configurations['total_widget']) && !empty($configurations['total_widget']))? $configurations['total_widget']: 1;
$sizeFile = (isset($configurations['size_file']) && !empty($configurations['size_file']))? $configurations['size_file']: 5000000;

 $ruta = INSTANCE_MEDIA_PATH.'/books/';

// Create folder if it doesn't exist
if( !file_exists($ruta) ) {
        FilesManager::createDirectory($ruta);
}

switch($action) {

    case 'list':
        Acl::checkOrForward('BOOK_ADMIN');

        $cm = new ContentManager();

        if (empty($page)) {
            $limit= "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit= "LIMIT ".($page-1) * ITEMS_PAGE .', '.ITEMS_PAGE;
        }

        if ($category == 'favorite') {
            $books = $cm->find_all('Book', 'favorite =1 AND available =1', 'ORDER BY position, created DESC '.$limit);

            if(!empty($books)) {
                foreach ($books as &$book) {
                    $book->category_name = $ccm->get_name($book->category);
                    $book->category_title = $ccm->get_title($book->category_name);
                }
            }
            if (count($books) != $numFavorites ) {
                m::add( sprintf(_("You must put %d books in the HOME widget"), $numFavorites));
            }

        } else {
            $books = $cm->find_by_category('Book', $category, '1=1',
                           'ORDER BY created DESC '.$limit);
        }

        $params = array(
            'page'=>$page, 'items'=>ITEMS_PAGE,
            'total' => count($books),
            'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$category
        );

        $pagination = \Onm\Pager\SimplePager::getPagerUrl($params);

        $tpl->assign(array(
            'pagination' => $pagination,
            'books' => $books
        ));

        $tpl->display('book/list.tpl');
    break;

    case 'new':
        Acl::checkOrForward('BOOK_CREATE');
        $tpl->display('book/new.tpl');
    break;

    case 'read': //habrá que tener en cuenta el tipo
        Acl::checkOrForward('BOOK_UPDATE');
        $book = new Book( $_REQUEST['id'] );
        $tpl->assign('book', $book);
        $tpl->assign('category', $book->category);
        $tpl->display('book/new.tpl');

    break;

    case 'update':

        Acl::checkOrForward('BOOK_UPDATE');
        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        $book = new Book($id);

        if(!empty($_FILES['file']['name'])) {
            $nombre_archivo = FilesManager::cleanFileName($_FILES['file']['name']);
            $archivo_temporal = $_FILES['file']['tmp_name'];

            // Move uploaded pdf
            $uploadStatusPdf = @move_uploaded_file($archivo_temporal, $ruta.$nombre_archivo);
            if($uploadStatusPdf){
                $nombre_archivo_swf = str_replace('pdf', 'swf', $nombre_archivo);
                exec('pdf2swf -O 1 '.$ruta.$nombre_archivo.' -o '.$ruta.$nombre_archivo_swf);
            }
        }

        if(!empty($_FILES['file_img']['name'])) {
               //Book image front
            $nombre_archivo_img = FilesManager::cleanFileName($_FILES['file_img']['name']);
            $archivo_temporal_img = $_FILES['file_img']['tmp_name'];
            $uploadStatusPdf_img = @move_uploaded_file($archivo_temporal_img, $ruta.$nombre_archivo_img);
        }

        if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $book->fk_user != $_SESSION['userid']) {
            m::add(_("You can't modify this book data because you don't have enought privileges.") );
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$id);
        } else {
            $book->update( $_POST );
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
    break;

    case 'create':

        Acl::checkOrForward('BOOK_CREATE');

        $nombre_archivo = FilesManager::cleanFileName($_FILES['file']['name']);
        $archivo_temporal = $_FILES['file']['tmp_name'];

        //Book image front
        $nombre_archivo_img = FilesManager::cleanFileName($_FILES['file_img']['name']);
        $archivo_temporal_img = $_FILES['file_img']['tmp_name'];

        // Move uploaded pdf
        $uploadStatusPdf = @move_uploaded_file($archivo_temporal, $ruta.$nombre_archivo);
        $uploadStatusPdf_img = @move_uploaded_file($archivo_temporal_img, $ruta.$nombre_archivo_img);


        $book = new Book();
        if ( ($uploadStatusPdf !== false) &&  $book->create( $_POST )) {
            $nombre_archivo_swf = str_replace('pdf', 'swf', $nombre_archivo);
            exec('pdf2swf -O 1 '.$ruta.$nombre_archivo.' -o '.$ruta.$nombre_archivo_swf);

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

        } elseif ( $_FILES['file']['size'] > $sizeFile ) {
             m::add( sprintf(_("Sorry, file can't upload. You must check file size.(< %sB)"), $sizeFile ));

        } else {
             m::add( sprintf(_("Sorry, file can't upload.")));
        }

        $tpl->display('book/list.tpl');

    break;

    case 'validate':

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);


        Acl::checkOrForward('BOOK_CREATE');


        // Create folder if it doesn't exist
        if( !file_exists($ruta) ) {
             FilesManager::createDirectory($ruta);
        }

        $nombre_archivo   = FilesManager::cleanFileName($_FILES['file']['name']);
        $archivo_temporal = $_FILES['file']['tmp_name'];
         //Book image front
        $nombre_archivo_img   = FilesManager::cleanFileName($_FILES['file_img']['name']);
        $archivo_temporal_img = $_FILES['file_img']['tmp_name'];

        // Move uploaded pdf
        $uploadStatusPdf     = @move_uploaded_file($archivo_temporal, $ruta.$nombre_archivo);
        $uploadStatusPdf_img = @move_uploaded_file($archivo_temporal_img, $ruta.$nombre_archivo_img);

        $book = new Book();
        if ( (!empty($_FILES['file']['name'])) && ($uploadStatusPdf !== false) ) {
            $nombre_archivo_swf = str_replace('pdf', 'swf', $nombre_archivo);
            exec('pdf2swf -O 1 '.$ruta.$nombre_archivo.' -o '.$ruta.$nombre_archivo_swf);
        } elseif ( $_FILES['file']['size'] > $sizeFile ) {
             m::add( sprintf(_("Sorry, file can't upload. You must check file size.(< %sB)"), $sizeFile ));

        } else {
             m::add( sprintf(_("Sorry, file can't upload.")));
        }
        if ( (!empty($_FILES['file_img']['name'])) && ($uploadStatusPdf_img !== false) ) {
            m::add( sprintf(_("Sorry, image file can't upload.")));
        }

        if (!empty($id)) {
            $book->update( $_POST );
        }else{
            $book->create( $_POST );
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$book->id.'&category='.$category.'&page='.$page);

    break;

    case 'delete':

        $id = filter_input(INPUT_POST,'id', FILTER_DEFAULT);
        Acl::checkOrForward('BOOK_DELETE');
        $book = new Book();
        $book->delete( $id );

         Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    break;

    case 'change_status':

        Acl::checkOrForward('BOOK_AVAILABLE');

        $book = new Book($_REQUEST['id']);
        //Publicar o no, comprobar num clic
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $book->set_available($status, $_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    break;

    case 'frontpage_status': //Publicar/Despublicar
        Acl::checkOrForward('BOOK_AVAILABLE');
        $book = new Book($_REQUEST['id']);

        $status = ($_REQUEST['status']==1)? 1: 0;
        $book->set_available($_REQUEST['id'], $status);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    break;

    case 'batchFrontpage':

        Acl::checkOrForward('BOOK_AVAILABLE');

        if(isset($_GET['selected_fld']) && count($_GET['selected_fld']) > 0) {
            $fields = $_GET['selected_fld'];

            $status = filter_input ( INPUT_GET, 'status' , FILTER_SANITIZE_NUMBER_INT );
            if (is_array($fields)) {
                foreach ($fields as $i) {
                    $book = new Book($i);
                    $book->set_available($status, $_SESSION['userid']);
                    if ($status == 0) {
                        $book->set_favorite($status);
                    }
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

    break;

    case 'batchDelete':

        Acl::checkOrForward('BOOK_DELETE');
        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
            $fields = $_REQUEST['selected_fld'];
            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $book = new Book($i);
                    $book->delete( $i );
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    break;

    case 'change_favorite':

        Acl::checkOrForward('BOOK_FAVORITE');
        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        $status = filter_input(INPUT_GET,'status',FILTER_VALIDATE_INT,
                                array('options' => array('default'=> 0)));
        $book = new Book($id);
        if ($book->available == 1) {
            $book->set_favorite($status);
        } else {
            m::add(_("This book is not published so you can't define it as favorite.") );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

    break;

    case 'save_orden_list':
          $orden = $_GET['orden'];
          if(isset($orden)){
               $tok = strtok($orden,",");
               $pos=1;
               while (($tok !== false) AND ($tok !=" ")) {
                    $book = new Book($tok);
                    $book->set_position($pos);
                    $tok = strtok(",");
                    $pos++;
               }
           }
          exit(0);
    break;

    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    break;
}
