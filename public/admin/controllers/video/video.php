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
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    implode(DIRECTORY_SEPARATOR, array( SITE_VENDOR_PATH,'Panorama')))
));

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);
$tpl->assign('page', $page);
/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('video');

$category = filter_input(INPUT_GET,'category',FILTER_SANITIZE_STRING);

if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING);

}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu( $category, $contentType);
if(empty($category)) {$category ='widget';}
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
            $limit = "LIMIT ".($page-1) * ITEMS_PAGE .', '.(ITEMS_PAGE+1);
        }

        if ($category == 'widget') { //Widget video
            $videos = $cm->find_all('Video', 'in_home = 1 AND available =1', 'ORDER BY  created DESC '. $limit);

            if (count($videos) != $numFavorites ) {
                m::add( sprintf(_("You must put %d videos in the HOME widget"), $numFavorites));
            }

            if(!empty($videos)){
                foreach ($videos as &$video) {
                    $video->category_name = $ccm->get_name($video->category);
                    $video->category_title = $ccm->get_title($video->category_name);
                }
            }

        } elseif ($category == 'all') {
            $videos = $cm->find_all('Video', 'available =1', 'ORDER BY created DESC '. $limit);

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

    case 'selecttype':

        $tpl->display('video/selecttype.tpl');

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
            try {

                $videoP = new \Panorama\Video($url);
                $information = $videoP->getVideoDetails();

                $tpl->assign('information', $information);
                $html_out = $tpl->fetch('video/partials/_video_information.tpl');

            } catch (Exception $e) {
                $html_out = _( "Can't get video information. Check the url");
            }
        }  else {
            $html_out = _("Please check the video url, seems to be incorrect");
        }
        Application::ajaxOut($html_out);


        break;

    case 'create':

        Acl::checkOrForward('VIDEO_CREATE');

        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

        if ($type === 'file') {

            // Check if the video file entry was completed
            if (isset($_FILES)
                && array_key_exists('video_file', $_FILES)
                && array_key_exists('name', $_FILES["video_file"])
            ) {

                $videoFileData = array(
                    'file_type' =>$_FILES["video_file"]["type"],
                    'file_path' =>$_FILES["video_file"]["tmp_name"],
                    'category' => filter_input(INPUT_POST, 'category'),
                    'available' => filter_input(INPUT_POST, 'available'),
                    'content_status' => filter_input(INPUT_POST, 'content_status'),
                    'title' => filter_input(INPUT_POST, 'title'),
                    'metadata' => filter_input(INPUT_POST, 'metadata'),
                    'description' => filter_input(INPUT_POST, 'description'),
                    'author_name' => filter_input(INPUT_POST, 'author_name'),
                );

                $video = new Video();
                try {
                    $video->createFromLocalFile($videoFileData);
                } catch (\Exception $e) {
                    m::add($e->getMessage());
                    Application::forward($_SERVER['SCRIPT_NAME']. '?action=new&type='.$type);
                }

            } else {
                m::add(_('There was a problem while uploading the file. Please check if you have completed all the form fields.'));
                Application::forward($_SERVER['SCRIPT_NAME']. '?action=new&type='.$type);
            }

        } elseif ($type == 'web-source') {

            if (!empty($_POST['information'])) {

                $video = new Video();
                $_POST['information'] = json_decode($_POST['information'], true);
                try {
                    $video->create($_POST);
                } catch (\Exception $e) {
                    m::add($e->getMessage());
                    Application::forward($_SERVER['SCRIPT_NAME']. '?action=new&type='.$type);
                }

            } else {
                m::add('There was an error while uploading the form, not all the required data was sent.');
                Application::forward($_SERVER['SCRIPT_NAME']. '?action=new&type='.$type);
            }
        } else {
            m::add('There was an error while uploading the form, the video type is not specified.');
            Application::forward($_SERVER['SCRIPT_NAME']. '?action=new&type='.$type);
        }

        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        Application::forward(
            $_SERVER['SCRIPT_NAME']
            . '?action=list_today&category='.filter_input(INPUT_POST, 'category')
            . '&page=' . $page
        );

        break;

    case 'validate':

        $continue = true;

    case 'update':

        Acl::checkOrForward('VIDEO_UPDATE');

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        $video = new Video($id);

        $_POST['information'] = json_decode($_POST['information'], true);

        if (!Acl::isAdmin()
            && !Acl::check('CONTENT_OTHER_UPDATE')
            && $video->pk_user != $_SESSION['userid'])
        {
            m::add(_("You can't modify this video because you don't have enought privileges.") );
        } else {
            $video->update( $_POST );
        }
        if ($continue) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$video->id);
        } else {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
        }

        break;

    case 'getRelations':

        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);

        $video = new Video($id);
        $relations=array();
        $msg ='';
        $relations = RelatedContent::get_content_relations($id);

        if (!empty($relations)) {
            $msg = sprintf(_("<br>The video has some relations"));
            $cm = new ContentManager();
            $relat = $cm->getContents($relations);
            foreach($relat as $contents) {
                $msg.=" <br>- ".strtoupper($contents->category_name).": ".$contents->title;
            }
            $msg.="<br> "._("Caution! Are you sure that you want to delete this video and its relations?");

            echo $msg;
        }

        exit(0);
        break;

    case 'delete':

        Acl::checkOrForward('VIDEO_DELETE');

        $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
        if (!empty($id)) {
            $video = new Video($id);
            //Delete relations
            $rel= new RelatedContent();
            $rel->delete_all($id);
            $video->delete( $id ,$_SESSION['userid'] );
        } else {
            m::add(_('You must give an id for delete the video.'), m::ERROR);
        }
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

    case 'batchFrontpage':

        Acl::checkOrForward('VIDEO_AVAILABLE');
        if(isset($_GET['selected_fld']) && count($_GET['selected_fld']) > 0) {
            $fields = $_GET['selected_fld'];

            $status = filter_input ( INPUT_GET, 'status' , FILTER_SANITIZE_NUMBER_INT );
            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $video = new Video($i);
                    $video->set_available($status, $_SESSION['userid']);
                    if($status == 0){
                        $video->set_favorite($status, $_SESSION['userid']);
                    }
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

        break;


    case 'batchDelete':
        Acl::checkOrForward('VIDEO_DELETE');

        if(isset($_GET['selected_fld']) && count($_GET['selected_fld']) > 0) {
            $fields = $_GET['selected_fld'];

            if (is_array($fields)) {
                $msg = _("Next albums have relations. Delete one by one");

                foreach($fields as $i ) {
                    $video = new Video($i);
                    $relations=array();
                    $relations = RelatedContent::get_content_relations( $i );

                    if (!empty($relations)) {
                        $msg .= " \"".$video->title."\", ";
                    } else {
                        $video->delete( $i, $_SESSION['userid'] );
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

            $video = new Video();
            $msg = $video->set_position($_positions, $_SESSION['userid']);

            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
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


    case 'content-list-provider':
    case 'related-provider':

        $items_page = s::get('items_per_page') ?: 20;
        $category = filter_input( INPUT_GET, 'category' , FILTER_SANITIZE_STRING, array('options' => array('default' => '0')) );
        $page = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_STRING, array('options' => array('default' => '1')) );
        $cm = new ContentManager();

        list($videos, $pager) = $cm->find_pages('Video', 'available=1 ',
                    'ORDER BY starttime DESC,  contents.title ASC ',
                    $page, $items_page, $category);

        $tpl->assign(array('contents'=>$videos,
                            'contentTypeCategories'=>$parentCategories,
                            'category' =>$category,
                            'contentType'=>'Video',
                            'pagination'=>$pager->links
                    ));

        $html_out = $tpl->fetch("common/content_provider/_container-content-list.tpl");
        Application::ajaxOut($html_out);

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
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($httpParams));

        break;

    case 'content-provider':

        $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING,   array('options' => array( 'default' => 'home')));
        $page     = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT,   array('options' => array( 'default' => 1)));

        if ($category == 'home') { $category = 0; }

        $cm = new  ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedOpinions = '';
        if (count($contentElementsInFrontpage) > 0) {
            $contentsExcluded    = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedOpinions = ' AND `pk_video` NOT IN ('.$contentsExcluded.')';
        }

        list($videos, $pager) = $cm->find_pages(
            'Video',
            'contents.available=1 ', 'ORDER BY created DESC ', $page, 5
        );


        $tpl->assign(array(
            'videos' => $videos,
            'pager'  => $pager,
        ));

        $tpl->display('video/content-provider.tpl');

        break;

    default:

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);

       break;
}
