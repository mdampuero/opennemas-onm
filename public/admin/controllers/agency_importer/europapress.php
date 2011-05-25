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
        
        $europapress = \Onm\Import\Europapress::getInstance();
        
        // Get the amount of minutes from last sync
        $minutesFromLastSync = $europapress->minutesFromLastSync();
        
        $categories = \Onm\Import\DataSource\Europapress::getOriginalCategories();
        
        $find_params = array(
            'category' => filter_input ( INPUT_GET,
                                        'find[category]' ,
                                        FILTER_SANITIZE_STRING,
                                        array('options' => array('default' => '*')) ),
            'title' => filter_input ( INPUT_GET,
                                     'find[title]',
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
        $error = filter_input ( INPUT_GET, 'error' , FILTER_SANITIZE_STRING );

        $tpl->assign('elements', $elements);
        $tpl->assign('categories', $categories);
        $tpl->assign('minutes', $minutesFromLastSync);
        //$tpl->assign('pagination', $pager);
        $tpl->assign('message', $message);
        $tpl->assign('error', $error);
        $tpl->display('agency_importer/europapress/list.tpl');

        break;
    
    case 'import':
        
        $httpParams []= array( 'message' => 'Action not implemented yet');
        
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));
    
        break;

    case 'sync': {
        
        try {
            
            $ftpConfig = array(
                'server'    => EUROPAPRESS_AUTH_SERVER,
                'user'      => EUROPAPRESS_AUTH_USERNAME,
                'password'  => EUROPAPRESS_AUTH_PASSWORD
            );
            
            $epSynchronizer = \Onm\Import\Europapress::getInstance();
            $message = $epSynchronizer->sync($ftpConfig);

        } catch (\Onm\Import\SynchronizationException $e) {
            $error = $e->getMessage();
        }
        
        $httpParams = array(
                            array('action' => 'list'),
                            array('page' => $page),
                            );
        if (isset($error)) { $httpParams []= array('error' => $error); }
        if( isset($message)) {
            $httpParams []= array(
                                  'message' => urlencode(sprintf(
                                                      _('Downloaded %d new articles and deleted %d old ones.'),
                                                        $message['deleted'],
                                                        $message['downloaded']
                                                        ))
                                );
        }
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
