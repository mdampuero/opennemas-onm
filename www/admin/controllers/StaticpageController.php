<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNemas project
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

/**
 * StaticpageController
 * 
 * @package    Controllers
 * @subpackage Backend
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: StaticpageController.php 1 2010-07-08 11:59:21Z vifito $
 */
class StaticpageController extends Onm_Controller_Action
{
    /**
     * Route: staticpages-index
     *  /staticpages/index/*
     */
    public function indexAction()
    {        
        $filter = $this->_getParam('filter', null);
        if( !is_null($filter) ) {
            $filter = '`title` LIKE "%' . $filter['title'] . '%"';
        }
        
        $cm = new ContentManager();
        $staticpages = $cm->find('Static_Page', 'status <> "REMOVED"', 'ORDER BY created DESC ');
        
        $this->tpl->assign('staticpages', $staticpages);
        
        // TODO: change this code to use Zend_Paginator
        
        $this->tpl->display('staticpage/index.tpl');
    }
    
    
    /**
     * Route: staticpage-create
     *  /staticpage/create/
     */  
    public function createAction()
    {
        if($this->getRequest()->isPost()) {
            
            $data = $this->getRequest()->getPost();
            
            // TODO: Validation
            $staticpage = new Static_Page();            
            $pk_content = $staticpage->create($data);            
            
            if( isset($data['categories']) ) {
                $staticpage->attachCategories($pk_content, $data['categories']);
            }
            
            $this->flashMessenger->addMessage(array('notice' => 'Static page added successfully.'));
            $this->redirector->gotoRoute( array(), 'staticpage-index' );
            
        } else {
            // Show form
            $this->tpl->display('staticpage/index.tpl');
        }
    }
    
    
    /**
     * Route: staticpage-read
     *  /staticpage/read/:id/
     */
    public function readAction()
    {
        // Nothing for backend
    }
    
    
    /**
     * Route: staticpage-update
     *  /staticpage/update/:pk_content/
     */
    public function updateAction()
    {
        $staticpage = new Static_Page();
        
        if($this->getRequest()->isPost()) {            
            $data = $this->getRequest()->getPost();
            
            try {
                $staticpage->update($data);
                
                $pk_content = $data['pk_content'];
                $staticpage->detachCategories($pk_content);
                $staticpage->attachCategories($pk_content, $data['categories']);
                
                $this->flashMessenger->addMessage(
                    array('notice' => 'Static page updated successfully.')
                );
            } catch(OptimisticLockingException $e) {
                $this->flashMessenger->addMessage(
                    array('warning' => 'Data values was not updated. Other user has done changes.')
                );
            } catch(Exception $e) {
                $this->flashMessenger->addMessage(
                    array('error' => $e->getMessage())
                );
            }
            
            $this->redirector->gotoRoute( array(), 'staticpage-index' );
            
        } else {
            // Load data & show form
            $pk_content = $this->_getParam('pk_content', 0);            
            $staticpage->read($pk_content);            
            
            $this->tpl->assign('staticpage', $staticpage);            
            $this->tpl->display('staticpage/index.tpl');
        }
        
    }
    
    
    /**
     * Route: staticpage-delete
     *  /staticpage/delete/:pk_content/
     */
    public function deleteAction()
    {
        $pk_content = $this->_getParam('pk_content', 0);
        
        $staticpage = new Static_Page();
        $staticpage->changeStatus($pk_content, 'REMOVED');
        
        
        $this->flashMessenger->addMessage(
            array('notice' => 'Static Page was sended to trash.')
        );
        $this->redirector->gotoRoute( array(), 'staticpage-index' );        
    }
    
    
    /**
     * Route: staticpage-changestatus
     *  /staticpage/changestatus/:pk_content/:status
     */
    public function changestatusAction()
    {
        $pk_content = $this->_getParam('pk_content', 0);
        $status = $this->_getParam('status', "PENDING");
        
        $staticpage = new Static_Page();        
        $staticpage->changeStatus($pk_content, $status);
        
        $this->redirector->gotoRoute( array(), 'staticpage-index' );
    }
    
    
}