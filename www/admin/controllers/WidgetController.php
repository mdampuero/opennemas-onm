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

class WidgetController extends Onm_Controller_Action
{
    
    public function init()
    {
        
    }
    
    
    /**
     * Route: widget-index
     *  /widget/index/*
     */
    public function indexAction()
    {
        $cm = new ContentManager();
        $widgets = $cm->find('Widget', 'fk_content_type=12', 'ORDER BY created DESC ');        
        
        // TODO: pagination        
        $this->tpl->assign('widgets', $widgets);
        
        $this->tpl->display('widget/index.tpl');
    }
    
    
    /**
     * Route: widget-read
     *  /widget/read/:id/
     */
    public function readAction()
    {        
        $id = $this->_getParam('id', 0);
        
        $widget = new Widget();
        $widget->read($id);        
        
        $this->tpl->assign('id', $id);
        $this->tpl->assign('widget', $widget);
        
        $this->tpl->display('widget/index.tpl');
    }
    
    
    /**
     * Route: widget-create
     *  /widget/create/
     */    
    public function createAction()
    {
        if($this->getRequest()->isPost()) {
            
            $data = $this->getRequest()->getPost();
            // TODO: Validation
            $widget = new Widget();
            $widget->create($data);
            
            $this->flashMessenger->addMessage(array('notice' => 'Widget added successfully.'));
            $this->redirector->gotoRoute( array(), 'widget-index' );
            
        } else {
            // Show form
            $this->tpl->display('widget/index.tpl');
        }
    }
    
    
    /**
     * Route: widget-update
     *  /widget/update/:id/
     */
    public function updateAction()
    {
        $widget = new Widget();
        
        if($this->getRequest()->isPost()) {            
            $data = $this->getRequest()->getPost();            
            $widget->update($data);
            
            $this->flashMessenger->addMessage(array('notice' => 'Widget added successfully.'));
            $this->redirector->gotoRoute( array(), 'widget-index' );            
        } else {
            // Load data & show form
            $id = $this->_getParam('id', 0);            
            $widget = $widget->read($id);
            
            $this->tpl->assign('widget', $widget);
            
            $this->tpl->display('widget/index.tpl');
        }
    }
    
    
    /**
     * Route: widget-delete
     *  /widget/delete/:id/
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $widget = new Widget();
        $widget->delete($id);
        
        $this->redirector->gotoRoute( array(), 'widget-index' );        
    }
    
    
    /**
     * Route: widget-changestatus
     *  /widget/toggle/:id/:status
     */
    public function changeStatusAction()
    {
        $id = $this->_getParam('id', 0);
        $status = $this->_getParam('status', 0);
        
        $widget = new Widget();        
        $widget->changeStatus($id, $status);
        
        $this->redirector->gotoRoute( array(), 'widget-index' );
    }
    
}