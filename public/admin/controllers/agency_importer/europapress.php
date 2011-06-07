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

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');
 
// Check ACL
require_once(SITE_CORE_PATH.'privileges_check.class.php');
if(!Acl::check('COMMENT_ADMIN')) {    
    Acl::deny();
}

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$page   = filter_input ( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 0)) );
$action = filter_input ( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );

switch($action) {
    
    case 'list':
        
        if(!Acl::check('COMMENT_ADMIN', 'EDIT')) {    
            Acl::deny();
        }
        
        $europapress = \Onm\Import\Europapress::getInstance();
        
        // Get the amount of minutes from last sync
        $minutesFromLastSync = $europapress->minutesFromLastSync();
        
        $categories = \Onm\Import\DataSource\Europapress::getOriginalCategories();
        
        $find_params = array(
            'category' => filter_input ( INPUT_GET,
                                        'filter[category]' ,
                                        FILTER_SANITIZE_STRING,
                                        array('options' => array('default' => '*')) ),
            'title' => filter_input ( INPUT_GET,
                                     'filter[title]',
                                     FILTER_SANITIZE_STRING,
                                     array('options' => array('default' => '*')) ),
        );
        
        $elements = $europapress->findAll($find_params);
        
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => 15,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($elements),
        );
        //$pager = Pager::factory($pager_options);
        
        $message   = filter_input ( INPUT_GET, 'message' , FILTER_SANITIZE_STRING );
        if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
            $error = $_SESSION['error'];
        } else {
            $error = '';
        }

        $tpl->assign('elements', $elements);
        $tpl->assign('categories', $categories);
        $tpl->assign('minutes', $minutesFromLastSync);
        //$tpl->assign('pagination', $pager);
        $tpl->assign('message', $message);
        $tpl->assign('error', $error);
        $tpl->display('agency_importer/europapress/list.tpl');

        break;
    
    case 'show':
        
        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT);

        try {
            
            $ep = new Onm\Import\Europapress();
            $element = $ep->findByID($id);
            
        } catch (Exception $e) {
            
            // Redirect the user to the list of articles and show him/her an error message
            $httpParams []= array( 'error' => sprintf(_('ID "%d" doesn\'t exist'),$id));
            Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));
            
        }
        
        $tpl->assign('element', $element);
        $tpl->display('agency_importer/europapress/show.tpl');
        break;
    
    case 'import':
        
        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT);
        
        $ep = new Onm\Import\Europapress();
        $element = $ep->findByID($id);
        
        
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
        
        if(is_string($newArticleID)) {
            
            $httpParams []= array( 'id' => $newArticleID,
                                  'action' => 'read');
            Application::forward(SITE_URL_ADMIN.'/article.php' . '?'.String_Utils::toHttpParams($httpParams));
            
        }        
        
        
    
        break;

    case 'sync': {
        try {
            try {
                
                try {
                    
                    $ftpConfig = array(
                        'server'    => EUROPAPRESS_AUTH_SERVER,
                        'user'      => EUROPAPRESS_AUTH_USERNAME,
                        'password'  => EUROPAPRESS_AUTH_PASSWORD
                    );
                    
                    $epSynchronizer = \Onm\Import\Europapress::getInstance();
                    $message = $epSynchronizer->sync($ftpConfig);
                    $epSynchronizer->updateSyncFile();
        
                } catch (\Onm\Import\SynchronizationException $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
                
            } catch (\Onm\Import\Synchronizer\LockException $e) {
                $_SESSION['error'] = $e->getMessage() 
                                    .sprintf(_('If you are sure <a href="%s?action=unlock">try to unlock it</a>'),$_SERVER['PHP_SELF']);
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $e = new \Onm\Import\Europapress();
            $e->unlockSync();
        }
        
        $httpParams = array(
                            array('action' => 'list'),
                            array('page' => $page),
                            );
        if( isset($message)) {
            $httpParams []= array(
                                  'message' => urlencode(sprintf(
                                                      _('Downloaded %d new articles and deleted %d old ones.'),
                                                        $message['downloaded'],
                                                        $message['deleted']
                                                        ))
                                );
        }
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
