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

class PanelController extends Onm_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('rss', 'xml')
                      ->initContext();
    }

    public function indexAction()
    {        
        // render view
    }
    
    
    /**
     * Route: /panel/rss/?url=http...
     * 
     * This action check request is an ajax request
     */
    public function rssAction()
    {        
        $request = $this->getRequest();        
        
        // Is a Ajax request and url param exists
        if(filter_has_var(INPUT_GET, 'url') && $request->isXmlHttpRequest()) {            
            $url = filter_input(INPUT_GET, 'url');
            $url = urldecode($url);
            
            $proxy = new Proxy();
            $proxy->cache->set_cache_life(60*60); // 1 hour
            
            // get content and return to browser 
            $proxy->get($url)->dump();                        
            
        } else {
            $request = new Zend_Controller_Request_Apache404();
            
            $front = Zend_Controller_Front::getInstance();
            $front->setRequest($request);
        }
    }
}
