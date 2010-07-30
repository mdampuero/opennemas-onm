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
 *  KeywordController
 *
 * @package    Controllers
 * @subpackage Backend
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: KeywordController.php 1 2010-07-30 11:59:21Z  $
 */
class Keyword_KeywordController extends Onm_Controller_Action
{
    public $terms = null;
    public $keyword = null;

    
    public function init()
    {
       
    }

    /**
     * Route: keyword-index
     *  /keyword/index/*
     */
    public function indexAction()
    {
        $filter = null;
        if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter']['pclave'])) {
            $filter = '`pclave` LIKE "%' . $_REQUEST['filter']['pclave'] . '%"';
        }
        $keyword = Keyword_Model_Keyword::getInstance();
        $this->terms = $keyword->getList($filter);

    }


    /**
     * Route: keyword-create
     *  /keyword/create/
     */
    public function createAction()
    {
        if($this->getRequest()->isPost()) {

            $data = $this->getRequest()->getPost();

            // TODO: Validation
            $keyword = new Keyword_Model_Keyword();
            $pk_keyword = $keyword->create($data);


            $this->flashMessenger->addMessage(array('notice' => 'Keyword added successfully.'));
            $this->redirector->gotoRoute( array(), 'keyword-keyword-index' );
       
        }
    }


    /**
     * Route: keyword-read
     *  /keyword/read/:pk_keyword/
     */
    public function readAction()
    {

    }


    /**
     * Route: keyword-update
     *  /keyword/update/:pk_keyword/
     */
    public function updateAction()
    {
        $this->keyword = new Keyword_Model_Keyword($data['pk_keyword']);

        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            try {
                $pk_keyword = $this->keyword->update($data);

                $this->flashMessenger->addMessage(
                    array('notice' => 'Keyword updated successfully.')
                );
            }  catch(Exception $e) {
                $this->flashMessenger->addMessage(
                    array('error' => $e->getMessage())
                );
            }

            $this->redirector->gotoRoute( array(), 'keyword-keyword-index' );

        } else {
            // Load data & show form
            $pk_keyword = $this->_getParam('pk_keyword', 0);
            $this->keyword->read($pk_keyword);

        }
    }


    /**
     * Route: keyword-delete
     *  /keyword/delete/:pk_keyword/
     */
    public function deleteAction()
    {
         
         $pk_keyword = $this->_getParam('pk_keyword', 0);
        
         if($pk_keyword) {

            try {
                $this->keyword = new Keyword_Model_Keyword($data['pk_keyword']);
                $pk_keyword = $this->keyword->delete($pk_keyword);

                $this->flashMessenger->addMessage(
                    array('notice' => 'Keyword delete successfully.')
                );
            } catch(Exception $e) {
                $this->flashMessenger->addMessage(
                    array('warning' => 'Keyword was not delete. ')
                );
            }

            $this->redirector->gotoRoute( array(), 'keyword-keyword-index' );

        }  
    }

     /**
     * Route: keyword-service
     *  /keyword/service/
     */
    public function serviceAction()
    {
        $term = $this->_getParam('term', '');

        $result = array();
        if(strlen($term) > 2) {
            $keyword =  Keyword_Model_Keyword::getInstance();
            // TODO: use Zend_Filter
            $term = preg_replace('/[%_"\']/', '', $term);
            $list = $keyword->getList( 'word LIKE "' . $term . '%"' );


            foreach($list as $it) {
                $result[] = array(
                    'id'    => $it->pk_keyword,
                    'label' => $it->word,
                    'value' => $it->word,
                );
            }
        }

        echo( json_encode($result) );
        exit(0);
    }
    
}