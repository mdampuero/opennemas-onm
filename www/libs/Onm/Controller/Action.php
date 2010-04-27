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
 
/**
 * Onm_Controller_Action
 * wrapper to Zend_Controller_Action class
 * 
 * @package    Onm
 * @subpackage Controller
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Action.php 1 2010-01-18 12:27:26Z vifito $
 */
class Onm_Controller_Action extends Zend_Controller_Action
{
    protected $redirector = null;
    protected $flashMessenger = null;
    protected $tpl = null;
    protected $conn = null;
    
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        
        $this->redirector     = $this->_helper->getHelper('Redirector');
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        
        if (Zend_Registry::isRegistered('tpl')) {
            $this->tpl  = Zend_Registry::get('tpl');
        }
        
        if (Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }        
    }
    
}