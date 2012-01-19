<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
use Onm\Settings as s,
    Onm\Message  as m;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

// Check ACL
Acl::checkOrForward('IMPORT_EFE');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$page   = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 1)) );
$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

/**
 * Check if module is configured, if not redirect to configuration form
*/
if (
    is_null(s::get('efe_server_auth'))
    && $action != 'config'
) {
    m::add(_('Please provide your EFE auth credentials to start to use your EFE Importer module'));
    $httpParams [] = array(
                        'action'=>'config',
                    );
    Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));
}

switch($action) {

    case 'config':

        if (count($_POST) <= 0) {
            if ($serverAuth = s::get('efe_server_auth')) {

                $message    = filter_input( INPUT_GET, 'message' , FILTER_SANITIZE_STRING );

                $tpl->assign(array(
                    'server'    => $serverAuth['server'],
                    'username' => $serverAuth['username'],
                    'password' => $serverAuth['password'],
                    'message' => $message,
                    'agency_string' => s::get('efe_agency_string'),
                    'sync_from' => array(
                        'no_limits' => _('No limit'),
                        '86400' => _('1 day'),
                        '172800' => sprintf(_('%d days'),'2'),
                        '259200' => sprintf(_('%d days'),'3'),
                        '345600' => sprintf(_('%d days'),'4'),
                        '432000' => sprintf(_('%d days'),'5'),
                        '518400' => sprintf(_('%d days'),'6'),
                        '604800' => sprintf(_('%d week'),'1'),
                        '1209600' => sprintf(_('%d weeks'),'2'),
                    ),
                    'sync_from_setting'=> s::get('efe_sync_from_limit'),
                ));

            }

            $tpl->display('agency_importer/efe/config.tpl');
        } else {

            $server     = filter_input( INPUT_POST, 'server' , FILTER_SANITIZE_STRING );
            $username   = filter_input( INPUT_POST, 'username' , FILTER_SANITIZE_STRING );
            $password   = filter_input( INPUT_POST, 'password' , FILTER_SANITIZE_STRING );
            $syncFrom   = filter_input( INPUT_POST, 'sync_from' , FILTER_SANITIZE_STRING );
            $agencyString   = filter_input( INPUT_POST, 'agency_string' , FILTER_SANITIZE_STRING );

            if (!isset($server) || !isset($username) || !isset($password)) {
                Application::forward(SITE_URL_ADMIN.'/controllers/agency_importer/efe.php' . '?action=config');
            }

            $serverAuth =  array(
                'server'    => $server,
                'username' => $username,
                'password' => $password,
            );

            if (s::set('efe_server_auth', $serverAuth)
                && s::set('efe_sync_from_limit', $syncFrom)
                && s::set('efe_agency_string', $agencyString))
            {
                m::add(_('EFE configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving EFE configuration'), m::ERROR);
            }

            Application::forward(SITE_URL_ADMIN.'/controllers/agency_importer/efe.php' . '?action=list');
        }

        break;

    case 'list':
 
        $efe = \Onm\Import\Efe::getInstance();

        // Get the amount of minutes from last sync
        $minutesFromLastSync = $efe->minutesFromLastSync();

        $categories = \Onm\Import\DataSource\NewsMLG1::getOriginalCategories();

        $find_params = array(
            'category' => filter_input(
                INPUT_GET, 'filter_category' , FILTER_SANITIZE_STRING,
                array('options' => array('default' => '*'))
            ),
            'title' => filter_input(
                INPUT_GET, 'filter_title', FILTER_SANITIZE_STRING,
                array('options' => array('default' => '*'))
            ),
        );



        $elements = $efe->findAll($find_params);

        $items_page = s::get('items_per_page') ?: 20;
        // Pager
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($elements),
        );
        $pager = Pager::factory($pager_options);

        $elements = array_slice($elements, ($page-1)*$items_page, $items_page);

        $urns = array();
        foreach ($elements as $element) {
            $urns []= $element->urn;
        }
        $alreadyImported = Content::findByUrn($urns);

        $tpl->assign(
            array(
                'elements'      =>  $elements,
                'already_imported' => $alreadyImported,
                'categories'    =>  $categories,
                'minutes'       =>  $minutesFromLastSync,
                'pagination'    =>  $pager,
            )
        );

        $tpl->display('agency_importer/efe/list.tpl');

        break;

    case 'show':

        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_STRING);

        try {

            $ep = new \Onm\Import\Efe();

            $element = $ep->findByFileName($id);

            $alreadyImported = Content::findByUrn($element->urn);

        } catch (Exception $e) {

            // Redirect the user to the list of articles and show him/her an error message
            $httpParams []= array( 'error' => sprintf(_('ID "%d" doesn\'t exist'),$id));
            Application::forward($_SERVER['PHP_SELF'] . '?'.String_Utils::toHttpParams($httpParams));

        }

        $tpl->assign(array(
            'element'   => $element,
            'imported'       => count($alreadyImported) > 0,
        ));
        $tpl->display('agency_importer/efe/show.tpl');
        break;

    case 'show_attachment':
        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_STRING);
        $attachment_id = filter_input ( INPUT_GET, 'attachment_id' , FILTER_SANITIZE_STRING);


        $ep = new Onm\Import\Efe();
        $element = $ep->findById($id);

        if ($element->hasPhotos()) {
            $photos = $element->getPhotos();

            $photo = $photos[$attachment_id];
            header("Content-type: ".$photo->file_type);
            echo file_get_contents(realpath($ep->syncPath.DIRECTORY_SEPARATOR.$photo->file_path));
            die();
        }

        break;

    case 'import_select_category':
        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_STRING);
        $category = filter_input ( INPUT_GET, 'category' , FILTER_SANITIZE_STRING);


        if (empty($id)) {
            m::add(_('Please specify the article to import.'), m::ERROR);
            Application::forward($_SERVER['PHP_SELF']."?action=list");
        }


        $ccm = ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu();


        $categories = array();
        foreach ($parentCategories as $category) {
            $categories [$category->pk_content_category]= $category->title;
        }

        $ep = new Onm\Import\Efe();
        $element = $ep->findByFileName($id);

        $tpl->assign(array(
            'id' => $id,
            'article' => $element,
            'categories' => $categories,
        ))  ;

        $tpl->display('agency_importer/efe/import_select_category.tpl');


        break;

    case 'import':

        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_STRING);
        $category = (int)filter_input ( INPUT_GET, 'category' , FILTER_SANITIZE_STRING);


        if (empty($id)) {
            m::add(_('Please specify the article to import.'), m::ERROR);
            Application::forward(SITE_URL_ADMIN."/controllers/agency_importer/efe.php");
        }

        if (empty($category)) {
            m::add(_('Please assign the category where import this article'), m::ERROR);
            Application::forward(SITE_URL_ADMIN."/controllers/agency_importer/efe.php?action=import_select_category&id={$id}&category={$category}");
        }
        $categoryInstance = new ContentCategory($category);
        if (!is_object($categoryInstance)) {
            m::add(_('The category you have chosen doesn\'t exists.'), m::ERROR);
            Application::forward(SITE_URL_ADMIN."/controllers/agency_importer/efe.php?action=import_select_category&id={$id}&category={$category}");
        }

        // Get EFE new from a filename
        $efe = new Onm\Import\Efe();
        $element = $efe->findByFileName($id);

        // If the new has photos import them
        if ($element->hasPhotos()) {
            $photos = $element->getPhotos();
            foreach($photos as $photo) {

                $filePath = realpath($efe->syncPath.DIRECTORY_SEPARATOR.$photo->file_path);

                // Check if the file exists
                if ($filePath) {
                    $data = array(
                        'title' => $photo->title,
                        'description' => '',
                        'local_file' => realpath($efe->syncPath.DIRECTORY_SEPARATOR.$photo->file_path),
                        'fk_category' => $category,
                        'category_name' => $categoryInstance->name,
                        'metadata' => String_Utils::get_tags($photo->title),
                    );

                    $photo = new Photo();
                    $photoID = $photo->createFromLocalFile($data);

                    // If this article has more than one photo take the first one
                    if (!isset($innerPhoto)) {
                        $innerPhoto = new Photo($photoID);
                    }
                }

            }
        }

        // If the new has videos import them
        if ($element->hasVideos()) {
            $videos = $element->getVideos();
            foreach($videos as $video) {

                $filepath = realpath($efe->syncPath.DIRECTORY_SEPARATOR.$video->file_path);

                // Check if the file exists
                if ($filePath) {
                    $videoFileData = array(
                        'file_type' => $video->file_type,
                        'file_path' => realpath($efe->syncPath.DIRECTORY_SEPARATOR.$video->file_path),
                        'category' => $category,
                        'available' => 1,
                        'content_status' => 0,
                        'title' => $video->title,
                        'metadata' => String_Utils::get_tags($video->title),
                        'description' => '',
                        'author_name' => 'internal',
                    );

                    $video = new Video();
                    $videoID = $video->createFromLocalFile($videoFileData);

                    // If this article has more than one video take the first one
                    if (!isset($innerVideo)) {
                        $innerVideo = new Video($videoID);
                    }
                }
            }
        }

        $values = array(
            'title' => $element->texts[0]->title,
            'category' => $category,
            'with_comment' => 1,
            'content_status' => 0,
            'frontpage' => 0,
            'in_home' => 0,
            'title_int' => $element->texts[0]->title,
            'metadata' => String_Utils::get_tags($element->texts[0]->title),
            'subtitle' => $element->texts[0]->pretitle,
            'agency' => s::get('efe_agency_string') ?: $element->agency_name,
            'summary' => $element->texts[0]->summary,
            'body' => $element->texts[0]->body,
            'posic' => 0,
            'id' => 0,
            'fk_publisher' => $_SESSION['userid'],
            'img1' => '',
            'img1_footer' => '',
            'img2' => (isset($innerPhoto) ? $innerPhoto->id : ''),
            'img2_footer' => (isset($innerPhoto) ? $innerPhoto->title : ''),
            'fk_video' => '',
            'fk_video2' => (isset($innerVideo) ? $innerVideo->id : ''),
            'footer_video2' => (isset($innerVideo) ? $innerVideo->title : ''),
            'ordenArti' => '',
            'ordenArtiInt' => '',
            'urn_source' => $element->urn,
        );

        $article = new Article();
        $newArticleID = $article->create($values);
        $_SESSION['desde']= 'efe_press_import';

        if(!empty($newArticleID)) {

            $httpParams []= array( 'id' => $newArticleID,
                                  'action' => 'read');
            Application::forward(SITE_URL_ADMIN.'/article.php' . '?'.String_Utils::toHttpParams($httpParams));

        }

        break;

    case 'sync': {
        try {

            $serverAuth = s::get('efe_server_auth');

            $ftpConfig = array(
                'server'    => $serverAuth['server'],
                'user'      => $serverAuth['username'],
                'password'  => $serverAuth['password'],
                'allowed_file_extesions_pattern' => '.*',
            );

            $efeSynchronizer = \Onm\Import\Efe::getInstance();
            $message = $efeSynchronizer->sync($ftpConfig);
            $efeSynchronizer->updateSyncFile();

            m::add(
                sprintf( _('Downloaded %d new articles and deleted %d old ones.'),
                        $message['downloaded'],
                        $message['deleted'])
            );

        } catch (\Onm\Import\SynchronizationException $e) {
            m::add($e->getMessage(), m::ERROR);
        } catch (\Onm\Import\Synchronizer\LockException $e) {
            $errorMessage = $e->getMessage()
                   .sprintf(_('If you are sure <a href="%s?action=unlock">try to unlock it</a>'),$_SERVER['PHP_SELF']);
            m::add( $errorMessage, m::ERROR );
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
            $e = new \Onm\Import\Efe();
            $e->unlockSync();
        }

        $httpParams = array(
                            array('action' => 'list'),
                            array('page' => $page),
                            );

        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

    } break;

    case 'unlock': {

        $e = new \Onm\Import\Efe();
        $e->unlockSync();
        unset($_SESSION['error']);
        $httpParams = array(
            array('action' => 'list'),
            array('page' => $page),
        );
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

    } break;



    default: {
        $httpParams = array(
            array('action','list'),
            array('page',$page),
        );
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($params));
    } break;
}
