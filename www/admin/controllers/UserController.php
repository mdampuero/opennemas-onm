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

class UserController extends Onm_Controller_Action
{
    public $message = null;

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {        
        
    }    
    
    public function loginAction()
    {        
        $request = $this->getRequest();
        xdebug_break();
        if($request->isPost()) {                                    
            $frm = $request->getPost(); // Form data
            
            $user = new User();            
            
            // Google authentication params
            $token   = (isset($frm['token']))?   $frm['token']:   null;
            $captcha = (isset($frm['captcha']))? $frm['captcha']: null;
            
            $result = $user->login($frm['login'], $frm['password'], $token, $captcha);
            
            if ($result === true) { // must be same type (===)
                
                if( isset($frm['rememberme']) ) {                    
                    setcookie("login_username", $frm['login'],    time()+60*60*24*30, '/admin/');
                    setcookie("login_password", $frm['password'], time()+60*60*24*30, '/admin/');
                } else {
                    if( isset($frm['login_username']) ) {
                        // Caducar a cookie
                        setcookie("login_username", '', time()-(60*60) );
                        setcookie("login_password", '', time()-(60*60) );
                    }
                } 
                
                // Load instance values into session
                $user->loadSession();
                
                // FIXME: Session expiration time has problems
                setcookie('default_expire', $user->sessionexpire, 0, '/admin/');                                
                Privileges_check::loadSessionExpireTime();                                                            
                
                // Redirect to panel
                $this->redirector->gotoRoute(array(), 'panel-index');
                
            } else {                
                // Show google captcha
                if(isset($result['token'])) {
                    $this->tpl->assign('token', $result['token']);
                    $this->tpl->assign('captcha', $result['captcha']);
                }
                
                // $this->flashMessenger->addMessage(array('notice' => 'Login Successful.'));
                $this->tpl->assign('message', 'Nome de usuario ou contrasinal incorrecto.');
            }            
        }
    }
    
    public function logoutAction()
    {
        Zend_Session::destroy();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }        
        
        $this->redirector->gotoRoute(array(), 'user-login');
    }
    
}
