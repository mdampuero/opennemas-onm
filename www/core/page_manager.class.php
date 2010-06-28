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
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: page_manager.class.php 1 2010-04-30 14:17:39Z vifito $
 */
class PageManager
{
    /**
     * @var PageManager
     */
    static private $instance = null;
    
    /**
     * @var ADOConnection
     */    
    private $conn = null;
    
    /**
     * @var MethodCacheManager
     */    
    public $cache = null;
    
    /**
     * @var array with pages
     */
    public $pages = null;
    
    
    /**
     * Implement a singleton
     * 
     * @access private
     */
    private function __construct()
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        if(Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }
        
        // TODO: evaluate «lazy loading» ¿?
        $this->pages = $this->populate();
    }
    
    
    /**
     * Get instance (singleton)
     *
     * @return PageManager
    */
    public function getInstance()
    {
        if(self::$instance == null) {
            self::$instance = new PageManager();
        }
        
        return self::$instance;
    }
    
    
    /**
     * Populate array of pages
     *
     * @return array
     */
    public function populate()
    {
        $sql = 'SELECT * FROM `pages` ORDER BY `fk_page` ASC, `weight` ASC';
        $rs  = $this->conn->Execute( $sql );
        
        if ($rs === false) {            
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return null;
        }
        
        // Clear previous categories
        $this->pages = array();        
        if($rs !== false) {
            while(!$rs->EOF) {
                $page = new Page();                
                $page->loadProperties($rs->fields);
                
                $this->pages[ $page->pk_page ] = $page;
                
                $rs->MoveNext();
            }
        }        
        
        // Return internal array to use cache
        return $this->pages;
    }
    
    
    /**
     * Get tree of pages from internal array $pages
     *
     * @param int $parent   Parent identifier which return subtree
     * @return array
     */
    public function getTree($parent = 0)
    {
        $tree = array();        
        
        $childs = $this->getChildren($parent);
        
        $i = 0;
        while(($page = array_shift($childs)) !== null) {
            $tree[$i]['element']    = $page;
            $tree[$i]['childNodes'] = $this->getTree($page->pk_page);
            
            $i++;
        }
        
        return $tree;
    }
    
    
    public function getRoot()
    {
        if(is_null($this->pages)) {
            throw new PageManagerException('Pages should be preloaded');
        }
        
        foreach($this->pages as $page) {
            if($page->fk_page == 0) {
                return $page;
            }
        }
        
        return null;
    }
    
    
    public function existsRoot()
    {
        return $this->getRoot() != null;
    }
    
    /**
     * Get page by $pk_page
     *
     * @param int $pk_page
     * @return Page
    */
    public function get($pk_page)
    {
        if(is_null($this->pages)) {
            throw new PageManagerException('Pages should be preloaded');
        }
        
        if(!isset($this->pages[$pk_page])) {
            return null;
        }
        
        return $this->pages[$pk_page];
    }
    
    
    /**
     *
    */
    public static function tree2html($root, $options, $level=0) {
        $html = '';        
        
        if(!isset($root['element'])) {
            
            // is a childNodes array
            $innerHTML = '';
            foreach($root as $tree) {
                
                if(!isset($options['level']) || ($options['level'] > $level)) { 
                    if( PageManager::_isVisibleItem($options['conditions'], $tree['element']) ) {
                        $innerHTML .= '<li id="page-' . $tree['element']->pk_page .'"';
                        
                        if(isset($options['active']) &&
                                 $options['active'] == $tree['element']->pk_page)
                        {
                            $innerHTML .= ' class="active"';
                        }
                        
                        $innerHTML .= '>';                        
                        
                        if(!isset($options['template'])) {
                            $innerHTML .= '<a href="' . $options['baseurl'] . $tree['element']->getPermalink() .'" ';
                            $innerHTML .= 'title="' . $tree['element']->title . '">';
                            
                            $innerHTML .= (strlen($tree['element']->menu_title)>0) ? $tree['element']->menu_title :$tree['element']->title;
                            
                            $innerHTML .= '</a>';
                        } else {
                            $tpl = Zend_Registry::get('tpl');
                            $tpl->assign('page', $tree['element']);
                            
                            $innerHTML .= $tpl->fetch($options['template']);
                        }
                        
                        if(isset($tree['childNodes'])) {
                            $innerHTML .= PageManager::tree2html($tree['childNodes'], $options, $level+1);
                        }
                        
                        $innerHTML .= '</li>';
                    }
                }
            }
            
            if(!empty($innerHTML)) {
                $html .= '<ul>' . $innerHTML . '</ul>';
            }            
        } 
        
        return $html;
    }
    
    
    /**
     * 
     * 
    */
    private function _isVisibleItem($conditions, $element)
    {        
        if(is_array($conditions)) {
            foreach($conditions as $prop => $validItems) {
                if(!in_array($element->{$prop}, $validItems)) {
                    
                    return false;
                }
            }
        }
        
        return true;
    }
    
    
    /**
     * Get childs
     *
     * @param int $pk_page
     * @return array
     */
    public function getChildren($pk_page)
    {
        if(is_null($this->pages)) {
            throw new PageManagerException('Pages should be preloaded');
        }
        
        $children = array();
        foreach($this->pages as $page) {
            if($page->fk_page == $pk_page) {
                $children[] = $page;
            }
        }
        
        return $children;
    }
    
    
    /**
     * Get a page by slug property
     *
     * @param string $slug
     * @return Page
     */
    public function getPageBySlug($slug)
    {        
        if(is_null($this->pages)) {
            throw new PageManagerException('Pages should be preloaded');
        }
        
        foreach($this->pages as $page) {
            if($page->slug == $slug) {
                return $page;
            }
        }
        
        return new Page;
    }
    
    
    /**
     * Get a page by slug property
     *
     * @param string $slug
     * @return Page
     */
    public function getPageBySlugFromDb($slug)
    {        
        $sql = 'SELECT * FROM pages WHERE slug = ?';
        $rs  = $this->conn->Execute($sql, array($slug));
        
        if( ($rs !== false) && (!$rs->EOF) ) {
            $page = new Page();
            $page->loadProperties($rs->fields);
            
            return $page;
        }
        
        return null;
    }
    
    
    /**
     * 
     */
    public function getContentsByPage($pk_page)
    {                
        $sql = 'SELECT * FROM `contents_pages` WHERE `pk_fk_page` = ? ORDER BY pk_placeholder, pk_weight ASC';
        $rs  = $this->conn->Execute($sql, array($pk_page));
        
        $contents = array();
        if($rs !== false) {
            while(!$rs->EOF) {
                $contents[ $rs->fields['pk_placeholder'] ][] = array(
                    'pk_content' => $rs->fields['pk_fk_content'],
                    'mask'       => $rs->fields['mask'],
                    'params'     => $rs->fields['params'],
                );
                
                $rs->MoveNext();
            }
        } else {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        }
        
        return $contents;
    }
    
    public function getContentsByPageSlug($slug)
    {
        $page = new Page();
        $current = $page->getPageBySlug($slug);
        
        $contents = $this->getContentsByPage( $current->pk_page );
        
        Zend_Registry::get('logger')->info($contents);
        
        return $contents;
    }
    
    
    public function getDescendants($pk_page)
    {
        $descendants = array();
        
        $childs = $this->getChildren($pk_page);        
        
        while(($page = array_shift($childs)) !== null) {
            $descendants[] = $page->pk_page;
            
            $descendants = array_merge($descendants, $this->getDescendants($page->pk_page));
        }
        
        return $descendants;
    }
    
    
    /**
     * Reset page, 
     *
     * @param int $pk_page
     * @return boolean
    */
    public function resetPage($pk_page)
    {
        $sql = 'DELETE FROM `contents_pages` WHERE `pk_fk_page`= ?';
        $rs  = $this->conn->Execute($sql, array($pk_page));
        
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        }
        
        return true;
    }
    
    public function attachContents($pk_page, $contents)
    {
        $bulkData = array();
        
        foreach($contents as $placeholder => $entries) {
            $weight = 0;
            foreach($entries as $entry) {
                $bulkData[] = array(
                    $pk_page,
                    $entry['pk_content'],
                    $placeholder,
                    $weight,
                    $entry['mask'],
                    null // TODO: implement params
                );
                
                $weight++;
            }            
        }
        
        $sql = 'INSERT INTO `contents_pages`
                (`pk_fk_page`, `pk_fk_content`, `pk_placeholder`, `pk_weight`, `mask`, `params`)
                VALUES (?, ?, ?, ?, ?, ?)';
        
        $rs = $this->conn->Execute($sql, $bulkData);
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        } else {
            Zend_Registry::get('logger')->info("funcionou");
        }
    }
    
    
    /**
     * Get html options to build a Html select
     * 
     * <code>
     * // Get tree
     * $tree = $pageMgr->getTree();
     *
     * // Get elements to disabled
     * $disabled = $pageMgr->getDescendants(1);
     * 
     * echo('<select>');
     * echo $pageMgr->getHtmlOptions(array('disabled' => $disabled, 'tabChar' => '&nbsp;&middot;&nbsp;'), $tree);
     * echo('</select>');
     * </code>
     *
     * @see CategoryManager::getHtmlOptions()
     * @param array $options
     * @param array $tree
     * @param int $level
     * @return string
    */
    public function getHtmlOptions($options, $tree, $level=0)
    {        
        $html = '';
        
        foreach($tree as $page) {
            
            $html .= '<option value="' . $page['element']->pk_page . '"';
            
            if( isset($options['disabled']) 
                    && in_array($page['element']->pk_page, $options['disabled']) ) {                
                $html .= ' disabled="disabled"';
            }
            
            if( isset($options['className']) ) {
                $html .= ' class="' . $options['className'] . '"';
            }
            
            if( isset($options['selected'])
                    && ($options['selected'] == $page['element']->pk_page)) {
                $html .= ' selected="selected"';
            }
            
            $html .= '>';
            
            if( isset($options['tabChar']) ) {                
                $html .= str_repeat($options['tabChar'], $level);
            }
            
            $html .= $page['element']->title;
            $html .= '</option>';
            
            $html .= $this->getHtmlOptions($options, $page['childNodes'], $level+1);
        }
        
        return $html;
    }
    

    /**
     * Generate a unique slug 
     *
     * @use Onm_Filter_Slug
     * @param string $title
     * @param int $excludeId
     * @return string
     */
    public function generateSlug($title, $excludeId=null)
    {
        $filter = new Onm_Filter_Slug();
        $slug   = $filter->filter($title);
        
        // TODO: evaluate performance
        // Get all slugs in database
        $slugs = $this->_getSlugs($excludeId);
        
        if(in_array($slug, $slugs)) {            
            $i = 1;            
            $new = $slug . '-' . $i;
            
            while( in_array($new, $slugs) ) {
                $i++;
                $new = $slug . '-' . $i;
            }
            
            return $new;
        }
        
        return $slug;
    }
    
    
    /**
     * Relocate pages on tree
     * 
     * <code>
     * [data] => Array (
     * //  [pk_page] => weight
     *     [7] => 0
     *     [8] => 0
     *     [9] => 1
     *     [10] => 2     
     * ) 
     * </code>
     * 
     * @param array $data
     * @return boolean  Return true if action was performed successfully. otherwise false
    */
    public function relocate($data)
    {
        $sql = 'UPDATE `pages` SET `weight` = ? WHERE `pk_page` = ?';
        
        $inputarr = array();
        if(isset($data['pk'])) {
            foreach($data['pk'] as $i => $pk) {
                $inputarr[] = array($data['weight'][$i], $pk);
            }
        }
        
        $rs = $this->conn->Execute($sql, $inputarr);
        
        return $rs !== false;
    }
    
    
    /**
     * Get all slugs from database (table `pages`)
     *
     * @param int|null $excludeId
     * @return array    Array of strings with slugs
     */
    private function _getSlugs($excludeId=null)
    {
        if(is_null($excludeId) || !is_numeric($excludeId)) {
            $excludeId = -1;
        }
        
        $sql   = 'SELECT `slug` FROM `pages` WHERE `pk_page` <> ' . $excludeId;
        $slugs = $this->conn->GetCol($sql);        
        
        return $slugs;
    }
}