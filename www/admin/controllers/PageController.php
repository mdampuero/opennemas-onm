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

class PageController extends Onm_Controller_Action
{
    
    public function init()
    {
        
    }
    
    
    public function preDispatch() 
    {
        $translator = Zend_Registry::get('Zend_Translate');
        $statuses = array(
            'STANDARD'    => $translator->_('Standard'),
            'NOT_IN_MENU' => $translator->_('Not in menu'),
            'EXTERNAL'    => $translator->_('External'),
            'SHORTCUT'    => $translator->_('Shortcut'),
        );
        
        $this->tpl->assign('statuses', $statuses);
    }
    
    
    public function indexAction()
    {
        // Page Manager instance
        $pageMgr = PageManager::getInstance();
        $tree = $pageMgr->getTree(0);
        
        $options = array(
            'conditions' => array(
                'status' => array('AVAILABLE', 'PENDING'),
                'type'   => array('STANDARD', 'SHORTCUT', 'EXTERNAL', 'NOT_IN_MENU'),
            ),
            
            'template' => 'page/list_row.tpl',
        );    
        
        $this->tpl->assign('list', $pageMgr->tree2html($tree, $options));
        
        $this->tpl->display('page/index.tpl');
    }
    
    
    public function createAction()
    {
        if($this->getRequest()->isPost()) {            
            $data = $this->getRequest()->getPost();
            
            // TODO: validation
            $page = new Page();            
            $page->create($data);                                    
            
            $this->flashMessenger->addMessage(array('notice' => 'Page «' . $data['title'] . '» created sucessfully.'));
            $this->redirector->gotoRoute( array(), 'page-index' );            
        } else {
            $this->tpl->display('page/index.tpl');
        }
    }
    
    
    /**
     * Route: page-update
     *  /page/update/*
     */
    public function updateAction()
    {
        $page = new Page();
        
        if($this->getRequest()->isPost()) {            
            $data = $this->getRequest()->getPost();
            
            try {
                $page->update($data, $data['pk_page']);
                
                $this->flashMessenger->addMessage(
                    array('notice' => 'Page updated successfully.')
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
            
            $this->redirector->gotoRoute( array(), 'page-index' );
            
        } else {
            // Load data & show form
            $id = $this->_getParam('id', 0);            
            $page->read($id);            
            
            $this->tpl->assign('page', $page);            
            $this->tpl->display('page/index.tpl');
        }
    }
    
    
    public function deleteAction()
    {
        $page = new Page();
        
        // TODO: Confirmation and Validation
        //if($this->getRequest()->isPost()) {
            $id = $this->_getParam('id', 0);
            $page->delete($id);
            
            $this->flashMessenger->addMessage(
                array('notice' => 'Page removed.')
            );
            
            $this->redirector->gotoRoute( array(), 'page-index' );
        /* } else {
            $id = $this->_getParam('id', 0);
            
            $page = new Page($id);
            
            $this->tpl->assign('page', $page);
            $this->tpl->display('page/delete-confirm.tpl');
        } */
    }
    
    
    /*
    public function indexAction()
    {
        $this->tpl->addScript('jstree/jquery.tree.js', 'head');
        $this->tpl->addScript('jstree/plugins/jquery.tree.contextmenu.js', 'head');
        
        $page = new Page();
        
        $root = $page->getRoot();
        $tree = $page->getTree($root->pk_page);
        
        
        
        $this->tpl->assign('tree',  Page::tree2html($tree));
        $this->tpl->display('page/index.tpl');
    }
    
    public function moveAction()
    {        
        $page = new Page();
        
        $pk_page = $this->_getParam('pk_page', 0);
        $fk_page = $this->_getParam('fk_page', 0);
        $weight = $this->_getParam('weight', 0);
        
        $page->moveNode($pk_page, $fk_page, $weight);        
    }
    
    public function renameAction()
    {
        $page = new Page();
        
        $pk_page = $this->_getParam('pk_page', 0);
        $title   = $this->_getParam('title', 'untitled');
        
        $page->renameNode($pk_page, $title);        
    }
    
    public function createAction()
    {
        $page = new Page();
        
        $title = $this->_getParam('title', 0);
        $fk_page = $this->_getParam('fk_page', 0);
        $weight = $this->_getParam('weight', 0);                
        
        $data = array('fk_page'     => $fk_page,
                      'title'       => $title,
                      'slug'        => $page->generateSlug($title),
                      'status'      => 'AVAILABLE',
                      'type'        => 'STANDARD',
                      'weight'      => $weight);
        
        $id = $page->create($data);
        $page->moveNode($id, $fk_page, $weight); 
        // FIXME: mejorar esto, comunicación por JSON
        echo $id;
    }
    
    public function deleteAction()
    {
        $page = new Page();        
        $pk_page = $this->_getParam('pk_page', 0);        
        $page->delete($pk_page); 
    } */
    
    /**
     * Service to create a slug for a title of a page
     *
     * Route: page-slugit
     *  /page/slugit/
     */
    public function slugitAction()
    {
        // Check if it's a request was performed via XmlHttpRequest
        if( $this->getRequest()->isXmlHttpRequest() ) {        
            $title   = $this->_getParam('title', '');
            $pk_page = $this->_getParam('pk_page', -1);
            
            $pageMgr = PageManager::getInstance();
            echo $pageMgr->generateSlug($title, $pk_page);            
        }
    }    
}
