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
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class WebdavController extends Onm_Controller_Action
{
    
    public function init()
    {        
        //require_once 'Sabre/autoload.php';    
        //
        //$auth = new Sabre_HTTP_BasicAuth();        
        //$result = $auth->getUserPass();
        //
        //$authSuccess = false;
        //
        //// Authenticate using table of users from database
        //if( ($result !== false) && Zend_Registry::isRegistered('conn') ) {
        //    $conn = Zend_Registry::get('conn');
        //    
        //    $u = $result[0];
        //    
        //    $sql = 'SELECT `password` FROM `users` WHERE `login` = ?';
        //    $pass = $conn->GetOne($sql, array($u));            
        //    
        //    if( !empty($pass) ) {
        //        $authSuccess = md5($result[1]) == $pass;
        //    } 
        //}
        //
        //if( !$authSuccess ) {
        //    $auth->requireLogin();
        //    exit(0);
        //}
    }
    
    
    /**
     * Route: webdav-index
     *  /webdav/*
     */
    public function indexAction()
    {
        require_once SITE_PATH . 'libs/Sabre/autoload.php';
        
        $rootDirectory = new Sabre_DAV_FS_Directory( SITE_PATH . 'media' );
        
        $tree = new Sabre_DAV_ObjectTree($rootDirectory);
        $server = new Sabre_DAV_Server($tree);
        $server->setBaseUri('/admin/webdav/');
        
        $method = strtolower( $server->httpRequest->getMethod() );
        
        //if(isset($_SESSION['userid'])) {
        //    $plugin = new Sabre_DAV_Browser_Plugin();
        //    $server->addPlugin($plugin);
        //} elseif($method == 'get') {
        //    $this->redirector->gotoRoute(array(), 'user-login');
        //    $this->redirector->redirectAndExit();
        //}
        
        $server->exec();
    }
    
}