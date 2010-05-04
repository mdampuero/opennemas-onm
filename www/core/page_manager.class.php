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
 * PageManager
 * 
 * @package    Onm
 * @subpackage 
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: page_manager.class.php 1 2010-04-30 14:17:39Z vifito $
 */
class PageManager
{
    static private $instance = null;
    private $conn = null;
    public $cache = null;
    
    private function __construct()
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        if(Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }        
    }
    
    public function getInstance()
    {
        if(self::$instance == null) {
            self::$instance = new PageManager();
        }
        
        return self::$instance;
    }
    
    /**
     * 
     */
    public function getContentsByPage($pk_page)
    {                
        $sql = 'SELECT * FROM `contents_pages` WHERE `pk_page` = ? ORDER BY pk_placeholder, pk_weight DESC';
        $rs  = $this->conn->Execute($sql, array($pk_page));
        
        $contents = array();
        if($rs !== false) {
            while(!$rs->EOF) {
                $contents[ $rs->fields['pk_placeholder'] ][] = array(
                    'pk_content' => $rs->fields['pk_fk_content'],
                    'mask'       => $rs->fields['mask'],
                    'params'     => $rs->fields['params'],
                );
            }
        }
        
        return $contents;
    }
    
    public function getContentsByPageSlug($slug)
    {
        $page = Page::getPageBySlug($slug);        
        return $this->getContentsByPage( $page->pk_page );
    }
    
}