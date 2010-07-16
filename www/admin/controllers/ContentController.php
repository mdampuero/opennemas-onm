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

class ContentController extends Onm_Controller_Action
{
    
    public function init()
    {
        $viewRenderer = $this->_helper->getHelper('viewRenderer');
        $viewRenderer->setNoRender(true);
    }
    
    
    /**
     * Service to create a slug for a title of a content
     *
     * Route: content-slugit
     *  /content/slugit/
     */
    public function slugitAction()
    {
        $title = $this->_getParam('title', '');
        $pk_content = $this->_getParam('pk_content', -1);
        
        $contMgr = new ContentManager();            
        echo $contMgr->slugIt($title, $pk_content);
    }
    
    
    /**
     * Service to search any content
     *
     * Route: content-search
     *  /content/search/
     */
    public function searchAction()
    {
        $data = $this->_getAllParams();
        
        $filter = new Onm_Filter_Unhyphenate();
        $data   = $filter->filter($data);
        
        $contentMgr = new ContentManager();
        
        $options = array(
            'select' => array('pk_content', 'title', 'description', 'slug', 'keywords', 'status')
        );
        
        $result = $contentMgr->search($data['q'], $options);
        
        $data = new Zend_Dojo_Data();
        $data->setIdentifier('pk_content')
             ->addItems($result);
        
        header('Content-Type: application/json');
        echo $data;
    }
    
    
}