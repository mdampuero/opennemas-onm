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
 * Page
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: page.class.php 1 2010-04-20 11:14:52Z vifito $
 */
class Page 
{
    
    private $pk_page;
    private $fk_page;
    private $title;
    private $slug;
    private $description;
    private $keywords;
    private $status;
    private $type;
    private $grid;
    private $theme;
    private $color;
    private $params;
    private $weight;    
    
    public  $cache = null;
    private $conn  = null;

    // Constructor
    public function __construct($pk_page=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        $this->conn  = Zend_Registry::get('conn');
        
        // Load page if pk_page is not null
        if(!is_null($pk_page)) {
            $this->read($pk_page);
        }                
    }
    
    // Getters & Setters
    public function __get($name)
    {
        if(property_exists($this, $name)) {
            return $this->{$name};
        }
        
        return null;
    }
    
    public function __set($name, $value)
    {
        if(property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }
    
    public function create($data)
    {
        // Get connection
        // $conn = Zend_Registry::get('conn');
        $conn = $GLOBALS['application']->conn;
        
        $fields = array('fk_page', 'title', 'slug', 'description',
                        'keywords', 'status', 'type', 'grid',
                        'theme', 'color', 'params', 'weight');
        
        $id = SqlHelper::bindAndInsert('pages', $fields, $data, $conn);
        
        return $id;
    }
    
    public function read($pk_page)
    {
        // Get connection
        // $conn = Zend_Registry::get('conn');
        $conn = $GLOBALS['application']->conn;
        
        $sql = 'SELECT * FROM pages WHERE pk_page=?';
        $rs  = $conn->Execute($sql, array($pk_page));        
        
        if(($rs !== false) && (!$rs->EOF)){
            $this->_loadProperties($rs->fields);
        }                
    }
    
    public function getRoot()
    {
        // Get connection
        // $conn = Zend_Registry::get('conn');
        $conn = $GLOBALS['application']->conn;
        
        $sql = 'SELECT pk_page FROM pages WHERE fk_page = 0 ORDER BY weight';
        $pk_page  = $conn->GetOne($sql);        
        
        if($pk_page === false) {
            return null;
        }
        
        return new Page($pk_page);
    }
    
    public function update($data, $pk_page)
    {
        // Get connection
        // $conn = Zend_Registry::get('conn');
        $conn = $GLOBALS['application']->conn;
        
        $filter = 'pk_page = ' . $pk_page;
        $fields = array('fk_page', 'title', 'slug', 'description',
                        'keywords', 'status', 'type', 'grid',
                        'theme', 'color', 'params', 'weight');
        
        SqlHelper::bindAndUpdate('pages', $fields, $data, $filter, $conn);        
    }
    
    /**
     * Delete a page,
     * page must not to have child pages
     *
     * @pre Page::hasChildPages($pk_page) == false
     * 
     * @param int $pk_page
     * @return boolean  Return true if page was removed
     */ 
    public function delete($pk_page)
    {
        if(!$this->hasChildPages()) {
            $sql = 'DELETE FROM pages WHERE pk_page = ?';
            
            // Get connection
            // $conn = Zend_Registry::get('conn');
            $conn = $GLOBALS['application']->conn;
            
            $rs = $conn->Execute($sql, array($pk_page));
            if($rs === false) {
                throw new Exception( $conn->ErrorMsg() );
            }
        }
        
        return false;
    }
    
    public function generateSlug($title)
    {
        $filter = new Onm_Filter_Slug();
        $slugs  = $this->_getSlugs();
        
        $slug = $filter->filter($title);
        
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
     * 
     */
    private function _getSlugs()
    {        
        $sql   = 'SELECT slug FROM pages';
        $slugs = $this->conn->GetCol($sql);
        
        return $slugs;
    }
    
    /**
     * Get a tree representation of pages 
     *
     * <code>
     * array
     *  'element' => 
     *     object(Page)[34]
     *       private 'pk_page' => string '7' (length=1)
     *       private 'fk_page' => string '0' (length=1)
     *       private 'title' => string 'HOME' (length=4)
     *       ...
     *   'childNodes' => 
     *     array
     *       0 => 
     *         array
     *           'element' =>
     *              object(Page)[37]
     *              ...
     * </code>
     * @param int $parent   Page Id to recovery subtree
     * @return array
     */
    public function getTree($parent=null)
    {
        // Get connection
        // $conn = Zend_Registry::get('conn');
        $conn = $GLOBALS['application']->conn;
        
        // O pai
        $sql = 'SELECT * FROM pages WHERE pk_page = ?';
        $rs  = $conn->Execute($sql, array($parent));
        
        // Added parent 
        $page = new Page();
        $this->_loadProperties($rs->fields, $page);
        
        $tree = array();
        $tree['element'] = $page;        
        
        $sql = 'SELECT * FROM pages WHERE fk_page = ? ORDER BY weight';
        $rs  = $conn->Execute($sql, array($parent));
        
        while(!$rs->EOF) {            
            $tree['childNodes'][] = $this->getTree($rs->fields['pk_page']);
            
            $rs->MoveNext();
        }
        
        return $tree;
    }
    
    /**
     * Get a page by slug property
     *
     * @param string $slug
     * @return Page
     */
    public function getPageBySlug($slug)
    {
        // Get connection
        // $conn = Zend_Registry::get('conn');
        $conn = $GLOBALS['application']->conn;
        
        $sql = 'SELECT * FROM pages WHERE slug = ?';
        $rs  = $conn->Execute($sql, array($slug));
        
        if( ($rs !== false) && (!$rs->EOF) ) {
            $page = new Page();
            $page->_loadProperties($rs->fields);
            
            return $page;
        }
        
        return null;
    }
    
    /**
     * hasChildPages, Check if a page has children
     *
     * @param int $pk_page  Page Id
     * @return boolean      Return true if has children, otherwise false
     */
    public function hasChildPages($pk_page=null)
    {
        // Check for static call with param pk_page 
        if(is_null($pk_page) && isset($this->pk_page)) {
            $pk_page = $this->pk_page;
        }
        
        // Get connection
        // $conn = Zend_Registry::get('conn');
        $conn = $GLOBALS['application']->conn;
        
        $sql = 'SELECT count(*) AS num_children FROM pages WHERE fk_page = ?';
        $rs = $conn->GetOne($sql, array($pk_page));
        
        if($rs === false) {
            throw new Exception( $conn->ErrorMsg() );
        }
        
        return $rs > 0;
    }
    
    /**
     * Load values in associative array to current object ($this)
     * 
     * @param array $assocProps     Associative array 
     */
    private function _loadProperties($assocProps, $object=null)
    {
        if(is_null($object)) {
            $object = $this;
        }
        
        foreach($assocProps as $prop => $val) {
            if(property_exists($object, $prop)) {
                $object->{$prop} = $val;
            }
        }        
    }
    
    /**
     *
     */
    public function moveNode($pk_page, $fk_page, $weight)
    {
        $sql = 'UPDATE pages SET fk_page=?, weight=? WHERE pk_page=?';
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        $logger->log(array($fk_page, $weight, $pk_page), Zend_Log::INFO);
        
        
        $rs = $this->conn->Execute($sql, array($fk_page, $weight, $pk_page));
        if($rs === false) {
            $logger->log($this->conn->ErrorMsg(), Zend_Log::INFO);
        }
        
        $sql = 'UPDATE pages SET weight = weight + 1 WHERE fk_page=? AND weight >= ? AND pk_page <> ?';
        $logger->log(array($weight, $fk_page, $pk_page), Zend_Log::INFO);
        
        $rs = $this->conn->Execute($sql, array($weight, $fk_page, $pk_page));
        if($rs === false) {
            $logger->log($this->conn->ErrorMsg(), Zend_Log::INFO);
        }
        
        $sql = 'SELECT pk_page,fk_page,weight FROM pages ORDER BY fk_page, weight';
        $rs = $this->conn->Execute($sql);
        
        $stmt = $this->conn->Prepare('UPDATE pages SET weight = ? WHERE pk_page = ?');
        if($rs !== false) {
            
            $fkPage = null;
            $i = 0;
            
            while(!$rs->EOF) {
                
                if($fkPage != $rs->fields['fk_page']) {
                    $fkPage = $rs->fields['fk_page'];
                    $i = 0;
                }
                
                $this->conn->Execute($stmt, array($i, $rs->fields['pk_page']));
                $logger->log($i . ' ' . $rs->fields['pk_page'], Zend_Log::INFO);
                $i++;
                
                $rs->MoveNext();
            }
        } else {
            $logger->log($this->conn->ErrorMsg(), Zend_Log::INFO);
        }
    }
    
    /**
     *
     *
     */
    public function renameNode($pk_page, $title)
    {
        $sql = 'UPDATE pages SET title=? WHERE pk_page=?';        
        $rs = $this->conn->Execute($sql, array($title, $pk_page));
        if($rs === false) {
            // log
        }        
    }
    
    public function render($slug)
    {
        
    }
    
    /**
     * Generate a HTML representatio for a tree of pages
     *
     * @param array $tree
     * @param string|null   $id
     * @param string $role  WAI-ARIA role
     * @param boolean $isRoot   Flag to control recursive generation
     * @return string   Tree HTML representation
     */
    /* public static function tree2html($tree, $id=null, $role='tree', $isRoot=true)
    {
        $html = '';
        
        if($isRoot) {
            if(is_null($id)) {
                $id = uniqid($role . '-');
            }
            
            // Role menu|tree
            $html = '<ul role="' . $role . '">';
            
            $html .= '<li id="node-' . $tree['element']->pk_page .'" class="open"
                          role="' . $role . 'item"
                          rel="root">';
            $html .= '<a href="/pages/' . $tree['element']->slug .'/">';
            $html .= '<ins>&nbsp;</ins>';
            $html .= $tree['element']->title;
            $html .= '</a>';
        } else {
            $html = '<ul role="group">';
            
            // Role menuitem|treeitem
            $html .= '<li id="node-' . $tree['element']->pk_page .'" role="' . $role . 'item"
                          rel="' . strtolower($tree['element']->type) .'">';                
            $html .= '<a href="/pages/' . $tree['element']->slug .'/">';
            $html .= '<ins>&nbsp;</ins>';
            $html .= $tree['element']->title;
            $html .= '</a>';            
        }        
        
        if(isset($tree['childNodes'])) {            
            foreach($tree['childNodes'] as $item) {
                $html .= Page::tree2html($item, null, $role, false);                
            }
        }
        
        $html .= '</li>';
        $html .= '</ul>';
        
        return $html;
    } */
    
    /**
     * Generate a HTML representatio for a tree of pages
     *
     * @param array $tree
     * @param string $role  WAI-ARIA role
     * @return string   Tree HTML representation
     */
    public static function tree2html($root, $role='tree') {
        $html = '';
        
        if(!isset($root['element'])) {
            // is a childNodes array            
            $html .= '<ul role="group">';            
            foreach($root as $tree) {                
                
                $html .= '<li id="node-' . $tree['element']->pk_page .'" role="' . $role . 'item"
                          rel="' . strtolower($tree['element']->type) .'">';                
                $html .= '<a href="/pages/' . $tree['element']->slug .'/">';
                $html .= '<ins>&nbsp;</ins>';
                $html .= $tree['element']->title;
                $html .= '</a>';
                
                if(isset($tree['childNodes'])) {
                    $html .= Page::tree2html($tree['childNodes'], $role);
                }                
                
                $html .= '</li>';
                
            }            
            $html .= '</ul>';
            
        } else {
            // isRoot
            $html .= '<ul role="' . $role . '">';
            
            $html .= '<li id="node-' . $root['element']->pk_page .'" class="open"
                          role="' . $role . 'item"
                          rel="root">';
            $html .= '<a href="/pages/' . $root['element']->slug .'/">';
            $html .= '<ins>&nbsp;</ins>';
            $html .= $root['element']->title;
            $html .= '</a>';
            
            if(isset($root['childNodes'])) {
                $html .= Page::tree2html($root['childNodes'], $role);
            }
            
            $html .= '</li></ul>';
        }
        
        return $html;
    }


}


// Test
//require_once('../config.inc.php');
//require_once('./application.class.php');
//require_once('./sql_helper.class.php');
//require_once('./method_cache_manager.class.php');
//
//Application::import_libs('*');
//$app = Application::load();

/* $data = array(
    'fk_page' => 0,
    'weight'  => 0,
    'title'   => 'HOME',
    'slug'    => 'home',
    'description' => 'Página principal',
    'keywords'    => 'home,opennemas',
    'status'  => 'AVAILABLE',
    'type'    => 'STANDARD',
    'weight'  => 0
);
$home = Page::create($data);

$data = array(
    'fk_page' => $home,
    'weight'  => 0,
    'title'   => 'Galicia',
    'slug'    => 'galiza',
    'description' => 'Página de galicia',
    'keywords'    => 'galicia,opennemas',
    'status'  => 'AVAILABLE',
    'type'    => 'STANDARD',
    'weight'  => 1
);
$galicia = Page::create($data);

$data = array(
    'fk_page' => $home,
    'weight'  => 0,
    'title'   => 'Outra páxina',
    'slug'    => 'paxina',
    'description' => 'Página outra',
    'keywords'    => 'outra,opennemas',
    'status'  => 'AVAILABLE',
    'type'    => 'STANDARD',
    'weight'  => 0
);
Page::create($data); 

$data = array(
    'fk_page' => $root->pk_page,
    'weight'  => 0,
    'title'   => 'Mundo',
    'slug'    => 'mundo',
    'description' => 'Página de mundo',
    'keywords'    => 'mundo,opennemas',
    'status'  => 'AVAILABLE',
    'type'    => 'STANDARD',
    'weight'  => 1
);
$mundo = Page::create($data);

$data = array(
    'fk_page' => $mundo,
    'weight'  => 0,
    'title'   => 'Mundo 1',
    'slug'    => 'mundo-1',
    'description' => 'Página de mundo 1',
    'keywords'    => 'mundo,opennemas',
    'status'  => 'AVAILABLE',
    'type'    => 'STANDARD',
    'weight'  => 1
);
$mundo_1 = Page::create($data);

$data = array(
    'fk_page' => $mundo_1,
    'weight'  => 0,
    'title'   => 'Mundo 1_1',
    'slug'    => 'mundo-1-1',
    'description' => 'Página de mundo 1_1',
    'keywords'    => 'mundo,opennemas',
    'status'  => 'AVAILABLE',
    'type'    => 'STANDARD',
    'weight'  => 1
);
$mundo_1_1 = Page::create($data);

$data = array(
    'fk_page' => $mundo_1_1,
    'weight'  => 0,
    'title'   => 'Mundo 1_1_1',
    'slug'    => 'mundo-1-1-1',
    'description' => 'Página de mundo 1_1_1',
    'keywords'    => 'mundo,opennemas',
    'status'  => 'AVAILABLE',
    'type'    => 'STANDARD',
    'weight'  => 1
);
Page::create($data); */

//$page = new Page();
//$root = $page->getRoot();
//$tree = $page->getTree($root->pk_page);
//echo Page::tree2html($tree);  // Static
//
//
//$mundo = $page->getPageBySlug('mundo');
//$tree = $page->getTree($mundo->pk_page);
//echo Page::tree2html($tree); 
//
//$mundo = $page->getPageBySlug('home');
//$tree = $page->getTree($mundo->pk_page);
//
//var_dump($tree);
//die();
//
//
//echo Page::tree2html($tree, 'menuId', 'menu');
//
//$pageToDelete = $page->getPageBySlug('mundo-1-1-1');
//$page->delete($pageToDelete->pk_page);

//$root = new Page(7);

