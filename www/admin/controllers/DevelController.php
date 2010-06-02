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

class DevelController extends Onm_Controller_Action
{
    
    public function init()
    {
        
    }
    
    /**
     * Load contents values to test grid
     */
    public function loadcontentsAction()
    {
        $queries = array();
        $total = $this->_getParam('total', 20);                
        
        $rs = $this->conn->Execute('DELETE FROM `contents` WHERE `pk_content` IN (' . implode(',', range(0, $total)) . ')');
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        }
        
        $rs = $this->conn->Execute('DELETE FROM `articles` WHERE `pk_article` IN (' . implode(',', range(0, $total)) . ')');
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        }
        
        $rs = $this->conn->Execute('DELETE FROM `contents_pages` WHERE `pk_fk_content` IN (' . implode(',', range(0, $total)) . ')');
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        }
        
        $content = "INSERT INTO `contents` (`pk_content`, `fk_content_type`, `title`, `description`, `keywords`, `starttime`, `endtime`, `created`, `changed`, `fk_author`, `fk_publisher`, `fk_user_last_editor`, `views`, `status`, `published`, `slug`, `version`) VALUES (%d, '1', 'Article %d', 'Description description description description description description description description description description description description description description description', 'description', NULL, NULL, NULL, NULL, '1', '1', '1', NULL, 'AVAILABLE', NULL, 'article-%d', '0')";
        
        $article = "INSERT INTO `articles` (`pk_article`, `summary`, `body`, `img1`, `subtitle`, `img1_footer`, `img2`, `img2_footer`, `agency`, `fk_video`, `with_comment`, `fk_video2`, `footer_video2`, `footer_video1`, `title_int`) VALUES ('%d', 'summary summary summary summary ', 'body body body body body body ', NULL, 'subtitle', NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '');";
        
        $contents_pages = "INSERT INTO `contents_pages` (`pk_fk_page`, `pk_fk_content`, `pk_placeholder`, `pk_weight`, `mask`, `params`) VALUES ('%d', '%d', '%s', '%d', 'article/frontpage_article_lateral', NULL);";                
        
        $pageMgr = PageManager::getInstance();
        $page = $pageMgr->getRoot();
        
        $placeholders = array('content-center', 'content-right', 'content-left');
        
        for($i=40; $i <= $total; $i++) {
            $q = sprintf($content, $i, $i, $i);
            $rs = $this->conn->Execute($q);
            if($rs === false) {
                $error_msg = $this->conn->ErrorMsg();
                Zend_Registry::get('logger')->emerg($error_msg);
            }
            
            $q = sprintf($article, $i);
            $rs = $this->conn->Execute($q);
            if($rs === false) {
                $error_msg = $this->conn->ErrorMsg();
                Zend_Registry::get('logger')->emerg($error_msg);
            }
            
            $k = array_rand($placeholders);
            $q = sprintf($contents_pages, $page->pk_page, $i, $placeholders[$k], $i);
            $rs = $this->conn->Execute($q);
            if($rs === false) {
                $error_msg = $this->conn->ErrorMsg();
                Zend_Registry::get('logger')->emerg($error_msg);
            }
        }
        
        $this->flashMessenger->addMessage(array('notice' => 'Sample articles loaded.'));
        $this->redirector->gotoRoute( array(), 'panel-index' );
    }
    
    
}