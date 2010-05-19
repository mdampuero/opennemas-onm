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
 * CategoryController
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: CategoryController.php 1 2010-05-18 16:55:55Z vifito $
 */
class CategoryController extends Onm_Controller_Action
{

    public function init()
    {
        
    }

    /**
     * Route: category-index
     *  /category/index/*
     */
    public function indexAction()
    {
        $catMgr = CategoryManager::getInstance();
        
        // TODO: Pagination
        
        $this->tpl->assign('catMgr', $catMgr);
        $this->tpl->assign('categories', $catMgr->getCategories());        
        
        $this->tpl->display('category/index.tpl');
    }
    
    
    /**
     * Route: category-create
     *  /category/create/
     */     
    public function createAction()
    {
        if($this->getRequest()->isPost()) {
            
            $data = $this->getRequest()->getPost();
            // TODO: Validation
            $category = new Category();
            $category->create($data);
            
            $this->flashMessenger->addMessage(array('notice' => 'Category added successfully.'));
            $this->redirector->gotoRoute( array(), 'category-index' );
            
        } else {
            // Show form
            $this->tpl->display('category/index.tpl');
        }
    }
    
    
    /**
     * Route: category-update
     *  /category/update/:id/
     */
    public function updateAction()
    {
        $category = new Category();
        
        if($this->getRequest()->isPost()) {            
            $data = $this->getRequest()->getPost();
            
            try {
                $category->update($data);
                
                $this->flashMessenger->addMessage(
                    array('notice' => 'Category updated successfully.')
                );            
            } catch(Exception $e) {
                $this->flashMessenger->addMessage(
                    array('error' => $e->getMessage())
                );
            }
            
            $this->redirector->gotoRoute( array(), 'category-index' );
            
        } else {
            // Load data & show form
            $id = $this->_getParam('id', 0);            
            $category->read($id);
            
            $this->tpl->assign('category', $category);            
            $this->tpl->display('category/index.tpl');
        }
    }
    
    
    /**
     * Route: category-delete
     *  /category/delete/:id/
     */
    public function deleteAction()
    {
        $pk_category = $this->_getParam('id', 0);
        
        $category = new Category();
        $category->delete($pk_category);        
        
        $this->flashMessenger->addMessage(
            array('notice' => 'Category was removed successfully.')
        );
        $this->redirector->gotoRoute( array(), 'category-index' );
    }
    
}
