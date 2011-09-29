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
\Onm\Module\ModuleManager::checkActivatedOrForward('VIDEO_MANAGER');

// Check if the user can admin video
Acl::checkOrForward('VIDEO_ADMIN');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Video Management');

//Testing Panorama
set_include_path(get_include_path(). PATH_SEPARATOR. SITE_LIBS_PATH.DIRECTORY_SEPARATOR.'Panorama');
require_once(implode(DIRECTORY_SEPARATOR, array('Zend','Gdata','YouTube.php')));
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    implode(DIRECTORY_SEPARATOR, array( SITE_VENDOR_PATH,'Panorama')))
));

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);

/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('video');

$category = filter_input(INPUT_GET,'category',FILTER_SANITIZE_STRING);

if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING);

}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu( $category, $contentType);
if(empty($category)) {$category ='favorite';}
$tpl->assign('category', $category);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
//TODO: ¿datoscat?¿
$tpl->assign('datos_cat', $categoryData);

/******************* GESTION CATEGORIAS  *****************************/


$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}
switch ($action) {

    case 'list':

        $cm = new ContentManager();

        $configurations = s::get('video_settings');
        $numFavorites = $configurations['total_widget'];

        if (empty($page)) {
            $limit = "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit = "LIMIT ".($page-1) * ITEMS_PAGE .', '.$numItems;
        }

        if ($category == 'favorite') { //Widget video
            $videos = $cm->find_all('Video', 'favorite = 1 AND available =1', 'ORDER BY  created DESC '. $limit);
            if (count($videos) != $numFavorites ) {
                m::add( sprintf(_("You must put %d videos in the HOME widget"), $numFavorites));
            }
            if(!empty($videos)){
                foreach ($videos as &$video) {
                    $video->category_name = $ccm->get_name($video->category);
                    $video->category_title = $ccm->get_title($video->category_name);
                }
            }

        } else {
            $videos = $cm->find_by_category(
                'Video',
                $category,
                'fk_content_type = 9 ', 'ORDER BY created DESC '.$limit
            );
        }
        $params = array(
            'page'=>$page, 'items'=>ITEMS_PAGE,
            'total' => count($videos),
            'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$category,
        );

        $pagination = \Onm\Pager\SimplePager::getPagerUrl($params);
        $tpl->assign( array(
            'pagination' => $pagination,
            'videos' => $videos )
        );

        $tpl->display('video/list.tpl');

    break;

    case 'new':

        $tpl->display('video/new.tpl');

    break;

    case 'read':
        Acl::checkOrForward('VIDEO_UPDATE');

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        if (empty($id)) {
            $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        }
        $video = new Video( $id );

        $tpl->assign('information', $video->information);
        $tpl->assign('video', $video);
        $tpl->display('video/new.tpl');

    break;

    case 'getVideoInformation':

        $url = filter_input(INPUT_GET,'url',FILTER_DEFAULT);
        $url = rawurldecode($url);
        if ($url) {
            $fetchedFromAPC = false;
            if (extension_loaded('apc')) {
                $information = apc_fetch(APC_PREFIX ."video_".$url, $fetchedFromAPC);
            }
            if (!$fetchedFromAPC) {
                try {

                    $videoP = new \Panorama\Video($url);
                    $information = $videoP->getVideoDetails();

                } catch (Exception $e) {
                    $html_out = _( "Can't get video information. Check url");
                }
            }

            $tpl->assign('information', $information);
            $html_out = $tpl->fetch('video/partials/_video_information.tpl');
            if (extension_loaded('apc')) {
                apc_store(APC_PREFIX ."video_".$url, $information);
            }
        }  else {
            $html_out = _("Please check the video url, seems to be incorrect");
        }
        Application::ajax_out($html_out);


    break;

    case 'create':

        Acl::checkOrForward('VIDEO_CREATE');
        $video = new Video();
        $_POST['information'] = json_decode($_POST['information'], true);
        if($video->create( $_POST )) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
        } else {
            $tpl->assign('errors', $video->errors);
        }
        $tpl->display('video/new.tpl');

    break;

    case 'update':

        Acl::checkOrForward('VIDEO_UPDATE');

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        $video = new Video($id);

        $_POST['information'] = json_decode($_POST['information'], true);

        if (!Acl::isAdmin()
            && !Acl::check('CONTENT_OTHER_UPDATE')
            && $video->pk_user != $_SESSION['userid'])
        {
            m::add(_("You can't modify this article because you don't have enought privileges.") );
        } else {
            $video->update( $_POST );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

    break;

    case 'validate':

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        $_POST['information'] = json_decode($_POST['information'],true);

        if (!$id) {

            Acl::checkOrForward('VIDEO_CREATE');
            $video = new Video();

            //Estamos creando un nuevo artículo
            if(!$video->create( $_POST )) $tpl->assign('errors', $video->errors);

        } else {
            Acl::checkOrForward('VIDEO_UPDATE');
            $video = new Video($id);
            if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $video->pk_user != $_SESSION['userid']) {
                m::add(_("You can't modify this article because you don't have enought privileges.") );
            }else{
                $video->update( $_POST );
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$video->id);

    break;

    case 'delete':

        Acl::checkOrForward('VIDEO_DELETE');

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        $video = new Video($id);
        $relationes=array();
        $msg ='';
        $relationes = Related_content::get_content_relations($id);
        if (!empty($relationes)) {
            $msg = sprintf(_("The video %s has some relations:",$video->title));
            $cm = new ContentManager();
            $relat = $cm->getContents($relationes);
            foreach($relat as $contents) {
                $msg.="\n - ".strtoupper($contents->category_name).": ".$contents->title;
            }
            $msg.="\n \n "._("Caution! Are you sure that you want to delete this video and its relations?");

        } else {
            $msg = sprintf(_("Do you want delete %s?",$video->title));
        }

        echo $msg;
        exit(0);

    break;

    case 'yesdel':

        Acl::checkOrForward('VIDEO_DELETE');

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        if ($id) {
            $video = new Video($_REQUEST['id']);
            //Delete relations
            $rel= new Related_content();
            $rel->delete_all($id);
            $video->delete( $id ,$_SESSION['userid'] );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);

    break;

    case 'set_position':

        $video = new Video($_REQUEST['id']);
        $video->set_position($_REQUEST['posicion'],$_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);

    break;

    case 'change_status':

        Acl::checkOrForward('VIDEO_AVAILABLE');

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        $video = new Video($id);
        //Publicar o no,
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        //$video->set_status($status,$_SESSION['userid']);
        $video->set_available($status, $_SESSION['userid']);
        if($status == 0){
            $video->set_favorite($status);
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$page);

    break;

    case 'change_favorite':

        Acl::checkOrForward('VIDEO_FAVORITE');
        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        $video = new Video($id);
        $msg = '';
        //Publicar o no,
        $status = ($_REQUEST['status']==1) ? 1: 0; // Evitar otros valores
        if ($video->available==1) {
            $video->set_favorite($status);
        } else {
            m::add(_("This video is not published so you can't define it as favorite.") );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$category);

    break;


    case 'mfrontpage':

        Acl::checkOrForward('VIDEO_AVAILABLE');
        if (isset($_REQUEST['selected_fld'])
            && count($_REQUEST['selected_fld'])>0)
        {
            $fields = $_REQUEST['selected_fld'];
            $status = ($_REQUEST['status']==1)? 1: 0;
            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $video = new Video($i);
                    $video->set_available($status, $_SESSION['userid']);
                    if($status == 0){
                        $video->set_favorite($status);
                    }
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

    break;

    case 'mdelete':

        Acl::checkOrForward('VIDEO_DELETE');
         $msg='';
        if (isset($_REQUEST['selected_fld'])
            && count($_REQUEST['selected_fld'])>0)
        {
            $fields = $_REQUEST['selected_fld'];
            if (is_array($fields)) {
                $msg = _("Next albums have relations. Delete one by one");

                foreach($fields as $i ) {
                    $video = new Video($i);
                    $relations=array();
                    $relations = Related_content::get_content_relations( $i );

                    if(!empty($relations)){
                        $alert =1;
                        $msg .= " \"".$video->title."\", ";
                    } else {
                       $video->delete( $i,$_SESSION['userid'] );
                    }
                    if (isset($alert) && !empty($alert)) {
                        $msg.=_("You can delete one by one!");
                        m::add( $msg );
                    }
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

    break;

    case 'config':

        $configurationsKeys = array(
            'video_settings',
        );

        $configurations = s::get($configurationsKeys);

        $tpl->assign(array(
            'configs'   => $configurations,
        ));

        $tpl->display('video/config.tpl');

    break;

    case 'save_config':
        Acl::checkOrForward('VIDEO_SETTINGS');

        unset($_POST['action']);
        unset($_POST['submit']);

        foreach ($_POST as $key => $value ) {
            s::set($key, $value);
        }

        m::add(_('Settings saved.'), m::SUCCESS);

        $httpParams = array(
            array('action'=>'list'),
        );
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

    break;

    default:

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);

    break;
}
