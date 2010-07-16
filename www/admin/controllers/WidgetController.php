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
 * WidgetController
 * 
 * @package    Controllers 
 * @subpackage Backend
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: WidgetController.php 1 2010-07-13 10:39:26Z vifito $
 */
class WidgetController extends Onm_Controller_Action
{
    /**
     * @var array  Array of Widget
     */
    public $widgets = null;
    
    /**
     * @var Widget
     */
    public $widget  = null;    
    
    
    /**
     * Route: widget-index
     *  /widget/index/*
     */
    public function indexAction()
    {
        $cm = new ContentManager();
        $this->widgets = $cm->find(
            'Widget',
            'status <> "REMOVED"',
            'ORDER BY created DESC '
        );
    }
    
    
    /**
     * Route: widget-read
     *  /widget/read/:id/
     */
    public function readAction()
    {        
        $id = $this->_getParam('id', 0);
        
        $this->widget = new Widget();
        $this->widget->read($id);                
    }
    
    
    /**
     * Route: widget-create
     *  /widget/create/
     */    
    public function createAction()
    {
        if ($this->getRequest()->isPost()) {
            
            $data = $this->getRequest()->getPost();
            // TODO: Validation
            $this->widget = new Widget();            
            $pkContent = $this->widget->create($data);            
            
            if (isset($data['categories']) ) {
                $this->widget->attachCategories(
                    $pkContent,
                    $data['categories']
                );
            }
            
            $this->flashMessenger->addMessage(
                array('notice' => 'Widget added successfully.')
            );
            $this->redirector->gotoRoute(array(), 'widget-index');
            
        }
    }
    
    
    /**
     * Route: widget-update
     *  /widget/update/:id/
     */
    public function updateAction()
    {
        $this->widget = new Widget();
        
        if ($this->getRequest()->isPost()) {            
            $data = $this->getRequest()->getPost();
            
            try {
                $this->widget->update($data);
                
                $pkContent = $data['pk_content'];
                
                $this->widget->bindCategories($pk_content, $categories);
                
                $this->flashMessenger->addMessage(
                    array('notice' => 'Widget updated successfully.')
                );
            } catch (OptimisticLockingException $e) {
                $this->flashMessenger->addMessage(
                    array(
                        'warning' => 'Data values was not updated.' .
                                     'Other user has done changes.'
                    )
                );
            } catch (Exception $e) {
                $this->flashMessenger->addMessage(
                    array('error' => $e->getMessage())
                );
            }
            
            $this->redirector->gotoRoute(array(), 'widget-index');
            
        } else {
            // Load data & show form
            $id = $this->_getParam('id', 0);            
            $this->widget->read($id);
        }
        
    }
    
    
    /**
     * Route: widget-delete
     *  /widget/delete/:id/
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        $this->widget = new Widget($id);
        
        if ($this->getRequest()->isPost()) {            
            $this->widget->changeStatus($id, 'REMOVED');            
            
            $this->flashMessenger->addMessage(
                array('notice' => 'Widget was sended to trash.')
            );
            $this->redirector->gotoRoute(array(), 'widget-index');
        }
    }
    
    
    /**
     * Route: widget-changestatus
     *  /widget/changestatus/:id/:status
     */
    public function changestatusAction()
    {
        $id = $this->_getParam('id', 0);
        $status = $this->_getParam('status', "PENDING");
        
        $this->widget = new Widget();        
        $this->widget->changeStatus($id, $status);
        
        $this->redirector->gotoRoute(array(), 'widget-index');
    }
    
}