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
 * FrontpageController
 * 
 * @package    Controllers
 * @subpackage Backend
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FrontpageController.php 1 2010-06-01 00:05:23Z vifito $
 */
class FrontpageController extends Onm_Controller_Action
{
    
    public function init()
    {
        $viewRenderer = $this->_helper->getHelper('viewRenderer');
        $viewRenderer->setNoRender(true);
    }
    
    public function editAction()
    {
        $pk_page = $this->_getParam('pk_page', -1);
        
        // FIXME: refactor this operations {{{
        $pageMgr = PageManager::getInstance();
        $page = $pageMgr->get($pk_page);
        
        $grid = Grid::getInstance($page);                
        $items = $pageMgr->getContentsByPage( $pk_page );        
        
        $contents = array();
        foreach($items as $placeholder => $cts) {
            
            $contents[$placeholder] = array();
            
            foreach($cts as $it) {
                $content = Content::get($it['pk_content']);
                
                $props = array(
                    'content'     => $content,
                    'mask'        => $it['mask'],
                    'page'        => $page,
                    'params'      => $it['params'],
                    'weight'      => $it['weight'],
                    'placeholder' => $it['placeholder'],
                );
                
                $box = new ContentBox($props);                
                $contents[$placeholder][] = $box;
            }
        }
        
        $args = array(
            'renderMask' => 'content_row_admin',
        );
        
        $output = $grid->render($contents, $args);        
        $this->tpl->assign('grid_content', $output);
        // }}}
        
        $this->tpl->assign('page', $page);
        $this->tpl->assign('positions', $grid->getPositions()); 
        
        $this->tpl->display('frontpage/index.tpl');
    }
    
    
    public function repaintAction()
    {
        $pk_content = $this->_getParam('pk_content', -1);
        $pk_page    = $this->_getParam('pk_page', -1);
        $name       = $this->_getParam('mask', '');
        
        $content = Content::get($pk_content);
        $page = new Page();
        
        $output = '';
        
        if(($page->read($pk_page) !== null) && ($content !== null)) {            
            $props = array(
                'page' => $page,
                'content' => $content,            
            );
            
            $mask   = new Mask($name, $props);
            $output = $mask->apply();
            
        } elseif($content !== null) {
            
            $output = $content->__toString();
        }
        
        echo $output;
    }
    
    
    public function savepositionsAction()
    {
        $request = $this->getRequest();
        if( $request->isXmlHttpRequest() ) {
            $data = $request->getParams();
            
            Zend_Registry::get('logger')->info($data);
            
            // TODO: check "version" page & transactional query
            $pageMgr = PageManager::getInstance();
            $pageMgr->resetPage($data['pk_page']);            
            $pageMgr->attachContents($data['pk_page'], $data['contents']);
        }
        
    }
    
    public function getmasksAction()
    {                
        $this->tpl->loadPlugin('smarty_function_mask_list');
        
        $pk_content = $this->_getParam('pk_content', -1);
        $pk_page    = $this->_getParam('pk_page', -1);
        
        $content = Content::get($pk_content);
        $page    = new Page($pk_page);        
        
        $params = array(
            'item' => $content,
            'page' => $page,
        );
        
        $output = smarty_function_mask_list($params, $this->tpl);
        echo $output;
    }
    
    
}