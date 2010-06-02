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

class KeywordController extends Onm_Controller_Action
{
    
    public function init()
    {
        
    }
    
    
    /**
     * Route: keyword-index
     *  /keyword/index/*
     */
    public function indexAction()
    {
        
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
            $keyword = Keyword::getInstance();
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