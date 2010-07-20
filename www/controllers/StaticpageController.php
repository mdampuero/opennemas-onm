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
 * @package    Controllers
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


class StaticpageController extends Onm_Controller_Action
{
    public $layoutContent = null;
    
    public function init()
    {
        //$viewRenderer = $this->_helper->getHelper('viewRenderer');
        //$viewRenderer->setNoRender(true);
    }
    
    
    /**
     * Render staticpage by slug
     *
     * Route: staticpage-index
     *  /
     */
    public function readAction()
    {        
        $pk_content = $this->getRequest()->getParam('pk_content');
        $staticPage = new Static_Page($pk_content);
        
        $this->layoutContent = PageManager::renderInnerContent($staticPage);        
        
        // Get internal page
        //$pkPage = $staticPage->getParam('innerpage');        
        //
        //if($pkPage != null) {
        //    try {
        //        $page = new Page($pkPage);
        //        // Set inner content & mask
        //        //$page->setInner($staticPage, 'static_page/simple');
        //        $mask = $staticPage->getParam('innermask');
        //        $page->setInner($staticPage, $mask);
        //        
        //        $content = $page->dispatch();                
        //        $this->layoutContent = $content;
        //        
        //    } catch(PageNotAvailableException $ex) {
        //        
        //        if(APPLICATION_ENV == 'production') {
        //            $this->redirector->gotoRoute( array(), 'index-home' );
        //            exit();
        //        } else {
        //            Zend_Registry::get('logger')->info($ex->getMessage());
        //        }            
        //    }
        //} else {
        //    $this->layoutContent = $staticPage->__toString();
        //}
        
        
    }
    
    
}