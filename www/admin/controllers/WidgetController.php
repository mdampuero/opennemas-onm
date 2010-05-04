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
        if($this->getRequest()->isPost()) {            
            $data = $this->getRequest()->getPost();
            $widget->update($data);
            
            $this->flashMessenger->addMessage(array('notice' => 'Widget added successfully.'));
            $this->redirector->gotoRoute( array(), 'widget-index' );            
        } else {
            // Load data
            $id = $this->_getParam('id', 0);
            
            $widget = new Widget();
            $widget->read($id);        
            
            $this->tpl->assign('id', $id);
            $this->tpl->assign('widget', $widget);
            
            // Show form
            $this->redirector->gotoRoute( array(), 'widget-index' );
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
     * Route: widget-toggle
     *  /widget/toggle/:id/
     */
    public function toggleAction()
    {
        $id = $this->_getParam('id', 0);
        $widget->read($id);
        
        $available = ($widget->available+1) % 2;
        $widget->set_available($available, $_SESSION['userid']);
        
        $request = $this->getRequest();
        
        if( $request->isXmlHttpRequest() ) {
            list($img, $text)  = ($available)? array('g', _('PUBLICADO')): array('r', _('PENDIENTE'));
            
            echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            exit(0);
        }
        
        $this->redirector->gotoRoute( array(), 'widget-index' );
    }
    
}