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

    public function indexAction()
    {
        $this->tpl->addScript('jstree/jquery.tree.js', 'head');
        $this->tpl->addScript('jstree/plugins/jquery.tree.contextmenu.js', 'head');
        
        $page = new Page();
        
        $root = $page->getRoot();
        $tree = $page->getTree($root->pk_page);                
        
        $this->tpl->assign('tree',  Page::tree2html($tree));
        $this->tpl->display('page.tpl');
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
    }
}
