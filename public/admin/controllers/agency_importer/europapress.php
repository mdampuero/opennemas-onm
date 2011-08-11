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
Acl::checkOrForward('IMPORT_EPRESS');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$page   = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 0)) );
$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

/**
 * Check if module is configured, if not redirect to configuration form
*/
if (
    is_null(s::get('europapress_server_auth'))
    && $action != 'config'
) {
    m::add(_('Please provide your Europapress auth credentials to start to use your Europapress Importer module'));
    $httpParams [] = array(
                        'action'=>'config',
                    );
    Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));
}

switch($action) {

    case 'config':

        if (count($_POST) <= 0) {
            if ($serverAuth = s::get('europapress_server_auth')) {

                $message    = filter_input( INPUT_GET, 'message' , FILTER_SANITIZE_STRING );

                $tpl->assign(array(
                    'server'    => $serverAuth['server'],
                    'username' => $serverAuth['username'],
                    'password' => $serverAuth['password'],
                    'message' => $message,
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
                    'sync_from_setting'=> s::get('europapress_sync_from_limit'),
                ));

            }

            $tpl->display('agency_importer/europapress/config.tpl');
        } else {

            $server     = filter_input( INPUT_POST, 'server' , FILTER_SANITIZE_STRING );
            $username   = filter_input( INPUT_POST, 'username' , FILTER_SANITIZE_STRING );
            $password   = filter_input( INPUT_POST, 'password' , FILTER_SANITIZE_STRING );
            $syncFrom   = filter_input( INPUT_POST, 'sync_from' , FILTER_SANITIZE_STRING );

            if (!isset($server) || !isset($username) || !isset($password)) {
                Application::forward(SITE_URL_ADMIN.'/controllers/agency_importer/europapress.php' . '?action=config');
            }

            $serverAuth =  array(
                'server'    => $server,
                'username' => $username,
                'password' => $password,
            );
            
            if (s::set('europapress_server_auth', $serverAuth)
                && s::set('europapress_sync_from_limit', $syncFrom))
            {
                m::add(_('Europapress configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving Europapress configuration'), m::ERROR);
            }

            Application::forward(SITE_URL_ADMIN.'/controllers/agency_importer/europapress.php' . '?action=list');
        }

        break;

    case 'list':

        if(!Acl::check('COMMENT_ADMIN', 'EDIT')) {
            Acl::deny();
        }

        $europapress = \Onm\Import\Europapress::getInstance();

        // Get the amount of minutes from last sync
        $minutesFromLastSync = $europapress->minutesFromLastSync();

        $categories = \Onm\Import\DataSource\Europapress::getOriginalCategories();

        $find_params = array(
            'category' => filter_input ( INPUT_GET, 'filter_category' , FILTER_SANITIZE_STRING,
                                        array('options' => array('default' => '*')) ),
            'title' => filter_input ( INPUT_GET, 'filter_title', FILTER_SANITIZE_STRING,
                                     array('options' => array('default' => '*')) ),
        );
        
        

        $elements = $europapress->findAll($find_params);

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
        
        $tpl->assign(
            array(
                'elements'      =>  $elements,
                'categories'    =>  $categories,
                'minutes'       =>  $minutesFromLastSync,
                'pagination'    =>  $pager,
            )
        );
        
        $tpl->display('agency_importer/europapress/list.tpl');

        break;

    case 'show':

        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_STRING);

        try {

            $ep = new \Onm\Import\Europapress();
            //$element = $ep->findByID($id);

            $element = $ep->findByFileName($id);

        } catch (Exception $e) {


            // Redirect the user to the list of articles and show him/her an error message
            $httpParams []= array( 'error' => sprintf(_('ID "%d" doesn\'t exist'),$id));
            Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

        }

        $tpl->assign('element', $element);
        $tpl->display('agency_importer/europapress/show.tpl');
        break;

    case 'import':

        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_STRING);

        $ep = new Onm\Import\Europapress();
        $element = $ep->findByFileName($id);


        $values = array(
                        'title' => $element->title,
                        'category' => 20,
                        'with_comment' => 1,
                        'content_status' => 0,
                        'frontpage' => 0,
                        'in_home' => 0,
                        'title_int' => $element->title,
                        'metadata' => String_Utils::get_tags($element->title),
                        'subtitle' => $element->pretitle,
                        'agency' => $element->agencyName,
                        'summary' => $element->summary,
                        'body' => $element->body,
                        'posic' => 0,
                        'id' => 0,
                        'fk_publisher' => $_SESSION['userid'],
                        'img1' => '',
                        'img1_footer' => '',
                        'img2' => '',
                        'img2_footer' => '',
                        'fk_video' => '',
                        'fk_video2' => '',
                        'footer_video2' => '',
                        'ordenArti' => '',
                        'ordenArtiInt' => '',
                        );

        $article = new Article();
        $newArticleID = $article->create($values);
        $_SESSION['desde']= 'europa_press_import';

        if(is_string($newArticleID)) {

            $httpParams []= array( 'id' => $newArticleID,
                                  'action' => 'read');
            Application::forward(SITE_URL_ADMIN.'/article.php' . '?'.String_Utils::toHttpParams($httpParams));

        }



        break;

    case 'sync': {
        try {

            $serverAuth = s::get('europapress_server_auth');

            $ftpConfig = array(
                'server'    => $serverAuth['server'],
                'user'      => $serverAuth['username'],
                'password'  => $serverAuth['password']
            );

            $epSynchronizer = \Onm\Import\Europapress::getInstance();
            $message = $epSynchronizer->sync($ftpConfig);
            $epSynchronizer->updateSyncFile();
            
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
            $e = new \Onm\Import\Europapress();
            $e->unlockSync();
        }
        
        $httpParams = array(
                            array('action' => 'list'),
                            array('page' => $page),
                            );
        
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

    } break;

    case 'unlock': {

        $e = new \Onm\Import\Europapress();
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
