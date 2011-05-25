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
        
        // Get the amount of minutes from last sync
        $minutesFromLastSync = \Onm\Import\Europapress::minutesFromLastSync();
        
        $categories = \Onm\Import\Europapress::getOriginalCategories();


        $tpl->assign('categories', $categories);
        $tpl->assign('minutes', $minutesFromLastSync);
        $tpl->display('agency_importer/europapress/list.tpl');

        break;
    
    case 'import':
    
        break;

    case 'sync': {
        
        try {
            
            $ftpConfig = array(
                'server'    => EUROPAPRESS_AUTH_SERVER,
                'user'      => EUROPAPRESS_AUTH_USERNAME,
                'password'  => EUROPAPRESS_AUTH_PASSWORD
            );
            
            $epSynchronizer = \Onm\Import\Europapress::getInstance($ftpConfig);
            $epSynchronizer->sync();

        } catch (\Onm\Import\SynchronizationException $e) {
            $error = $e->getMessage();
        }
        
        $httpParams = array(
                            array('action' => 'list'),
                            array('page' => $page),
                            );
        if (isset($error)) { $httpParams [] = array('message' => $error); }
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
