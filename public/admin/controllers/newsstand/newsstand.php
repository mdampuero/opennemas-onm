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
\Onm\Module\ModuleManager::checkActivatedOrForward('KIOSKO_MANAGER');

 // Check if the user can admin kiosko
Acl::checkOrForward('KIOSKO_ADMIN');


/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', _('Kiosko Management'));

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);
if (!isset($page)) {
    $page = filter_input( INPUT_POST, 'page' , FILTER_VALIDATE_INT, array('options' => array('default' => '1')) );
}

/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('kiosko');

if(!defined('KIOSKO_DIR'))
    define('KIOSKO_DIR', "kiosko".SS);



$category = filter_input(INPUT_GET,'category',FILTER_SANITIZE_STRING);

if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING, array('options' => array('default' => 'favorite')));
}


    $ccm = ContentCategoryManager::get_instance();
    list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);


$tpl->assign('category', $category);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);

$tpl->assign('datos_cat', $categoryData);

/******************* GESTION CATEGORIAS  *****************************/

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

$tpl->assign('formAttrs', 'enctype="multipart/form-data"');

switch($action) {
    case 'list':
        $cm = new ContentManager();
        if ($category == 'favorite') {
            list($portadas, $pager)= $cm->find_pages('Kiosko',
                                        'fk_content_type=14 AND kioskos.favorite=1 ',
                                        'ORDER BY position ASC, date DESC ',$page, ITEMS_PAGE );


        } else {

            // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
            list($portadas, $pager)= $cm->find_pages('Kiosko', 'fk_content_type=14 ',
                                            'ORDER BY  date DESC ',$page, ITEMS_PAGE,
                                            $category);
        }
        $aut=new User();
        foreach ($portadas as $portada) {


            $portada->publisher=$aut->get_user_name($portada->fk_publisher);
            $portada->editor=$aut->get_user_name($portada->fk_user_last_editor);
        }
        $tpl->assign('portadas', $portadas);
        $tpl->assign('paginacion', $pager);
        $tpl->assign('KIOSKO_IMG_URL', INSTANCE_MEDIA.KIOSKO_DIR);

        $tpl->display('newsstand/list.tpl');


    break;

    case 'new':
        Acl::checkOrForward('KIOSKO_CREATE');
        $tpl->display('newsstand/new.tpl');

    break;

    case 'read':

        Acl::checkOrForward('KIOSKO_UPDATE');
        $cm = new ContentManager();
        $kiosko = new Kiosko( $_REQUEST['id'] );

        $tpl->assign('kiosko', $kiosko);
        $tpl->assign('KIOSKO_IMG_URL', INSTANCE_MEDIA.KIOSKO_DIR);
        $tpl->display('newsstand/read.tpl');

    break;

    case 'update':

        Acl::checkOrForward('KIOSKO_UPDATE');

        $_POST['fk_user_last_editor']=$_SESSION['userid'];
        $portada = new Kiosko($_POST['id']);

        $_POST['description'] = '';
        if (!Acl::isAdmin()
            && !Acl::check('CONTENT_OTHER_UPDATE')
            && $kiosko->fk_user != $_SESSION['userid'])
        {
             m::add(_("You can't modify this article because you don't have enought privileges.") );
        }
        $portada->update( $_POST );

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
    break;

    case 'create':

        Acl::checkOrForward('KIOSKO_CREATE');
        $_POST['fk_publisher'] = $_SESSION['userid'];

        //Se crea el nombre del PDF
        $date = new DateTime($_POST['date']);
        $_POST['name'] = $date->format('dmyhis').'-'.$_POST['category'].'.pdf';
        $_POST['path'] = $date->format('Ymd').'/';
        $ruta = INSTANCE_MEDIA_PATH. KIOSKO_DIR. $_POST['path'];

        // Create folder if it doesn't exist
        if( !file_exists($ruta) ) {
            FilesManager::createDirectory($ruta);
        }

        // Move uploaded file
        $uploadStatus = @move_uploaded_file($_FILES['file']['tmp_name'], $ruta. $_POST['name']);

        $kiosko = new Kiosko();

        if ($uploadStatus !== false) {
            if( !$kiosko->create( $_POST )) {
                m::add(_('There was a problem with kiosko data. Try again') );
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
        } else {
            if ($uploadStatus==false) {

                m::add(_('There was an error while uploading the file. <br />Try to upload files smaller than that size or contact with your administrator'),
                (int)(ini_get('upload_max_filesize').' ') );

                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
            }
            //Error debido a portada ya subida enla fecha indicada
            $_REQUEST['action'] = 'new';

            $tpl->assign('kiosko', $kiosko);
        }

    break;

    case 'delete':

        Acl::checkOrForward('KIOSKO_DELETE');
        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        if (!empty($id)) {
            $portada = new Kiosko($id);

            $portada->delete($id);
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
    break;

    case 'change_status':
        Acl::checkOrForward('KIOSKO_AVAILABLE');

        $portada = new Kiosko($_REQUEST['id']);

        //Publicar o no, comprobar num clic
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores

        $portada->set_available($status, $_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
    break;

    case 'change_favorite':

        Acl::checkOrForward('KIOSKO_HOME');

        $portada = new Kiosko($_REQUEST['id']);

        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        if ($portada->available==1) {
                $portada->set_favorite($status);
        } else {
                m::add(_("Can't be favorite. It's umpublished") );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
    break;

    case 'batchDelete':
        Acl::checkOrForward('KIOSKO_DELETE');

        if(isset($_GET['selected_fld']) && count($_GET['selected_fld']) > 0) {
            $fields = $_GET['selected_fld'];

            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $portada = new Kiosko($i);
                    $portada->delete( $i, $_SESSION['userid'] );
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letter_status=' .
                    $letterStatus . '&page=' . $page);
    break;

    case 'batchFrontpage':

        Acl::checkOrForward('KIOSKO_AVAILABLE');
        if(isset($_GET['selected_fld']) && count($_GET['selected_fld']) > 0) {
            $fields = $_GET['selected_fld'];

            $status = filter_input( INPUT_GET, 'status' , FILTER_SANITIZE_NUMBER_INT );
            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $portada = new Kiosko($i);
                    $portada->set_available($status, $_SESSION['userid']);
                    if($status == 0){
                        $portada->set_favorite($status, $_SESSION['userid']);
                    }
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

    break;

    case 'save_positions':
        $positions = $_GET['positions'];
        if (isset($positions)  && is_array($positions)
                && count($positions) > 0) {

           $_positions = array();
           $pos = 1;

           foreach($positions as $id) {
                    $_positions[] = array($pos, '1', $id);
                    $pos += 1;

            }

            $portada = new Kiosko();
            $msg = $portada->set_position($_positions, $_SESSION['userid']);
         }
         if(!empty($msg) && $msg == true) {
             echo _("Positions saved successfully.");
         } else{
             echo _("Have a problem, positions can't be saved.");
         }
        exit(0);
    break;

    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
    break;
}
