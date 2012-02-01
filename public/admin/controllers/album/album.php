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
\Onm\Module\ModuleManager::checkActivatedOrForward('ALBUM_MANAGER');

 // Check if the user can admin album
Acl::checkOrForward('ALBUM_ADMIN');

// Register events
require_once('./albums_events.php');

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', _('Album Management'));

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);

/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('album');

$category = filter_input(INPUT_GET,'category',FILTER_SANITIZE_STRING);

if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING);
}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);

if(empty($category)) {$category ='widget';}

$tpl->assign('category', $category);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);

$tpl->assign('datos_cat', $categoryData);

/******************* GESTION CATEGORIAS  *****************************/

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

switch($action) {

    case 'list':
        Acl::checkOrForward('ALBUM_ADMIN');

        $configurations = s::get('album_settings');
        $numFavorites = $configurations['total_widget'];

        if (empty($page)) {
            $limit= "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit= "LIMIT ".($page-1) * ITEMS_PAGE .', '.(ITEMS_PAGE+1);
        }

        $cm = new ContentManager();

        if ($category == 'widget') {
            $albums = $cm->find_all('Album', 'in_home =1 AND available =1',
                                'ORDER BY position ASC, created DESC '.$limit);
            if (count($albums) != $numFavorites ) {
                m::add( sprintf(_("You must put %d albums in the HOME widget"), $numFavorites));
            }
            if(!empty($albums)) {
                foreach ($albums as &$album) {
                    $album->category_name = $ccm->get_name($album->category);
                    $album->category_title = $ccm->get_title($album->category_name);
                }
            }

        } elseif ($category === 'all') {
            $albums = $cm->find_all('Album', 'available =1', 'ORDER BY  created DESC '.$limit);
            if(!empty($albums)) {
                foreach ($albums as &$album) {
                    $album->category_name = $ccm->get_name($album->category);
                    $album->category_title = $ccm->get_title($album->category_name);
                }
            }
        } else {
            $albums = $cm->find_by_category('Album', $category, 'fk_content_type=7',
                           'ORDER BY created DESC '.$limit);
        }

        $params = array(
            'page'=>$page, 'items'=>ITEMS_PAGE,
            'total' => count($albums),
            'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$category
        );

        $pagination = \Onm\Pager\SimplePager::getPagerUrl($params);

        $tpl->assign(array(
            'pagination' => $pagination,
            'albums' => $albums
        ));

        $tpl->display('album/list.tpl');

    break;

    case 'new':

        Acl::checkOrForward('ALBUM_CREATE');

        $configurations = s::get('album_settings');

        $tpl->assign( array(
            'crop_width' => $configurations['crop_width'],
            'crop_height' => $configurations['crop_height'] ));

        $tpl->display('album/new.tpl');

    break;

    case 'read':

        Acl::checkOrForward('ALBUM_UPDATE');

        $configurations = s::get('album_settings');
        $tpl->assign(array(
            'crop_width' => $configurations['crop_width'],
            'crop_height' => $configurations['crop_height']
        ));

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        if (empty($id)) { //because forwards
            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        }

        $album = new Album( $id);
        $tpl->assign('album', $album);

        $cropExist = file_exists(MEDIA_IMG_PATH_WEB.$album->cover);
        $tpl->assign('crop_exist', $cropExist);

        $photoData = array();
        $photos = $album->get_album($id);
        $tpl->assign('otherPhotos', $photos);
        if (!empty($photos)) {
            foreach ($photos as $ph) {
                $photoData[] = new Photo($ph[0]);
            }
        }
        $tpl->assign( array(
            'category' => $album->category,
            'photoData' => $photoData,
        ));
        $tpl->display('album/new.tpl');

    break;

    case 'create':
        var_dump($_POST);die();

        Acl::checkOrForward('ALBUM_CREATE');
        $album = new Album();
        if ($album->create( $_POST )) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
        }else{
             m::add(_($album->errors) );
        }
        $tpl->display('album/new.tpl');

    break;

    case 'update':
        var_dump($_POST);die();

        Acl::checkOrForward('ALBUM_UPDATE');

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        $album = new Album($id);

        if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $album->fk_user != $_SESSION['userid']) {
            m::add(_("You can't modify this content because you don't have enought privileges.") );
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$id);
        } else {
            $album->update( $_POST );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

    break;

    case 'validate':
        var_dump($_POST);die();

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        if(empty($id)) {

        Acl::checkOrForward('ALBUM_CREATE');

        $album = new Album();
        if (!$album->create( $_POST ))
            m::add(_($album->errors));
        } else {

            Acl::checkOrForward('ALBUM_UPDATE');
            $album = new Album($id);
            if (!Acl::isAdmin()
                && !Acl::check('CONTENT_OTHER_UPDATE')
                && $album->fk_user != $_SESSION['userid'])
            {
                 m::add(_("You can't modify this article because you don't have enought privileges.") );
            }
            $album->update( $_POST );

        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$album->id.'&category='.$category.'&page='.$page);

    break;

    case 'delete':

        Acl::checkOrForward('ALBUM_DELETE');

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);

        $album = new Album($id);

        $relations=array();
        $msg ='';
        $relations = Related_content::get_content_relations( $id );
        if (!empty($relations)) {
            $msg = sprintf(_('The album "%s" has related elements')."\n", $album->title);
            $cm= new ContentManager();
            $relat = $cm->getContents($relations);
            foreach($relat as $contents) {
               $msg.=" - ".strtoupper($contents->category_name).": ".$contents->title." \n";
            }
            $msg.="\n \n "._("Caution! Are you sure that you want to delete this album and all related contents?");
        } else {
            $msg = sprintf(_('Do you want to delete "%s"?'), $album->title);
        }
        echo $msg;
        exit(0);

    break;

    case 'yesdel':

        Acl::checkOrForward('ALBUM_DELETE');

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        if ($id){
            $album = new Album($id);
            //Delete relations
            $rel= new Related_content();
            $rel->delete_all($id);
            $album->delete($id,$_SESSION['userid'] );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$album->category.'&page='.$_REQUEST['page']);

    break;


    case 'change_status':

        Acl::checkOrForward('ALBUM_AVAILABLE');

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        $status = filter_input(INPUT_GET,'status',FILTER_VALIDATE_INT,
                                array('options' => array('default'=> 0)));
        $album = new Album($id);
        $album->set_available($status, $_SESSION['userid']);
        if($status == 0){
            $album->set_favorite($status);
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

    break;

    case 'change_favorite':

        Acl::checkOrForward('ALBUM_FAVORITE');

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        $status = filter_input(INPUT_GET,'status',FILTER_VALIDATE_INT,
                                array('options' => array('default'=> 0)));
        $album = new Album($id);
        if ($album->available == 1) {
            $album->set_favorite($status);
        } else {
            m::add(_("This album is not published so you can't define it as favorite.") );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

        break;

      case 'change_inHome':

        Acl::checkOrForward('ALBUM_HOME');

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        $status = filter_input(INPUT_GET,'status',FILTER_VALIDATE_INT,
                                array('options' => array('default'=> 0)));
        $album = new Album($id);
        if ($album->available == 1) {
            $album->set_inhome($status,$_SESSION['userid']);
        } else {
            m::add(_("This album is not published so you can't define it as widget home content.") );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

        break;


    case 'mfrontpage':

        Acl::checkOrForward('ALBUM_AVAILABLE');
        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
        {
            $fields = $_REQUEST['selected_fld'];
            $status = ($_REQUEST['id']==1)? 1: 0; //Se reutiliza el id para pasar el estatus
            if (is_array($fields)) {
                foreach ($fields as $i) {
                    $album = new Album($i);
                    $album->set_available($status, $_SESSION['userid']);
                    if ($status == 0) {
                        $album->set_favorite($status);
                    }
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

    break;

    case 'mdelete':

        Acl::checkOrForward('ALBUM_TRASH');
        if (isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
        $fields = $_REQUEST['selected_fld'];
        if (is_array($fields)) {

            $msg = _("These albums have relations:");

            foreach ($fields as $i ) {
                $album = new Album($i);
                $relations=array();
                $relations = Related_content::get_content_relations( $i );

                if(!empty($relations)){
                     $alert =1;
                     $msg .= " \"".$album->title."\", ";

                }else{
                    $album->delete( $i,$_SESSION['userid'] );
                }
            }
            if (isset($alert) && !empty($alert)) {
                $msg.=_("You must delete them one by one!");
                m::add( $msg );
            }
        }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.' &page='.$page);

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

            $album = new Album();
            $msg = $album->set_position($_positions, $_SESSION['userid']);

            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            require_once(SITE_CORE_PATH.'template_cache_manager.class.php');
            $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('home|0');
         }
         if(!empty($msg) && $msg == true) {
             echo _("Positions saved successfully.");
         } else{
             echo _("Unable to save the new positions. Please contact with your system administrator.");
         }
        exit(0);
    break;

    case 'config':

        $configurationsKeys = array('album_settings',);
        $configurations = s::get($configurationsKeys);
        $tpl->assign(array(
            'configs'   => $configurations,
        ));

        $tpl->display('album/config.tpl');

    break;

    case 'save_config':

        Acl::checkOrForward('ALBUM_SETTINGS');

        unset($_POST['action']);
        unset($_POST['submit']);

        foreach ($_POST as $key => $value ) { s::set($key, $value); }

        m::add(_('Settings saved successfully.'), m::SUCCESS);

        $httpParams = array(array('action'=>'list'),);
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

    break;

    default:

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

    break;
}