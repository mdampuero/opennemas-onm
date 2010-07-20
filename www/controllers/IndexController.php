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

class IndexController extends Onm_Controller_Action
{
    public $layoutContent = null;
    
    public function init()
    {
        
    }
    
    
    /**
     * Render home page
     *
     * Route: index-home
     *  /
     */
    public function homeAction()
    {
        // Get "homepage" (root page of tree)
        $pageMgr = PageManager::getInstance();        
        $page = $pageMgr->getRoot();        
        
        if($page != null) {
            $content = $page->dispatch();            
            $this->layoutContent = $content;
        } else {
            // TODO: redirect to unavailable page
            $this->layoutContent = '<h1>You must create a root page for this site.</h1>';
        }
        
    }
    
    
}