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
 * @package    Core
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Page
 *
 * <code>
 * $data = array(
 *    'fk_page' => 0,
 *   'weight'  => 0,
 *   'title'   => 'HOME',
 *   'slug'    => 'home',
 *   'description' => 'Home page',
 *   'keywords'    => 'home,opennemas',
 *   'status'  => 'AVAILABLE',
 *   'type'    => 'STANDARD',
 *   'weight'  => 0
 * );
 * $home = Page::create($data);
 *
 * $data = array(
 *    'fk_page' => $home, // reference to parent
 *    'weight'  => 0,
 *    'title'   => 'Child page',
 *    'slug'    => 'child',
 *    'description' => 'Secondary page',
 *    'keywords'    => 'child,opennemas',
 *    'status'  => 'AVAILABLE',
 *    'type'    => 'STANDARD',
 *    'weight'  => 1
 * );
 * $second = Page::create($data);
 *
 * $page = new Page();
 * $root = $page->getRoot();
 * $tree = $page->getTree($root->pk_page);
 * echo Page::tree2html($tree);  // Static
 * 
 * $pageToDelete = $page->getPageBySlug('child');
 * $page->delete( $pageToDelete->pk_page );
 * </code>
 *
 * @package    Core
 * @subpackage FrontManager
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
    private $inline_styles;
    private $params;
    private $weight;
    
    private $fk_author;
    private $fk_publisher;
    private $fk_user_last_editor;
    private $hits;
    private $starttime;
    private $endtime;
    private $created;
    private $changed;
    private $published;
    private $menu_title;
    private $short_url;
    private $version;
    
    public  $cache = null;
    private $conn  = null;
    
    private $_inner = null;
    
    /**
     * Constructor
     *
     * @param int $pk_page|null
     * @uses MethodCacheManager
     * @uses Zend_Registry
     */
    public function __construct($pk_page=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        $this->conn  = Zend_Registry::get('conn');
        
        // Load page if pk_page is not null
        if(!is_null($pk_page)) {
            $this->read($pk_page);
        }
    }
    
    
    /**
     * Getter magic method 
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if(property_exists($this, $name)) {
            return $this->{$name};
        }
        
        return null;
    }
    
    
    /**
     * Setter magic method
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if(property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }
    
    
    /**
     * Create a new page
     * 
     * @uses Page::prepareData()
     * @uses SqlHelper::bindAndInsert()
     * @throws SqlHelperException
     * @param array $data   Properties, from request, to bind with this object
     * @return int  Autoincrement ID from database
     */
    public function create($data)
    {        
        // Prepare array data with default values if it's necessary
        $this->prepareData($data);
        
        $fields = array(
            'fk_page', 'title', 'menu_title', 'short_url',
            'slug', 'description', 'keywords',
            'grid', 'theme', 'params', 'weight', 'status', 'type',
            'inline_styles',
            'fk_author', 'fk_publisher', 'fk_user_last_editor',
            'hits', 'starttime', 'endtime',
            'created', 'changed', 'published', 
        );
        
        $data['params'] = serialize($data['params']);
        
        $id = SqlHelper::bindAndInsert('pages', $fields, $data, $this->conn);
        
        return $id;
    }
    
    
    /**
     * Prepare default values to insert/update in database
     *
     * @param array $data
     */
    public function prepareData($data)
    {
        $pageMgr = PageManager::getInstance();
        
        $session = new Zend_Session_Namespace();
        
        $defaults = array(
            'fk_page' => $pageMgr->getRoot()->pk_page,  
            'weight'  => 0,
            'status'  => 'PENDING',
            'type'    => 'STANDARD',
            'hits'    => 0,
            
            'starttime' => '0000-00-00 00:00:00',
            'endtime'   => '0000-00-00 00:00:00',
            
            'created'   => date('Y-m-d H:i:s'),
            'changed'   => date('Y-m-d H:i:s'),
            'published' => date('Y-m-d H:i:s'),
            
            'fk_author'    => $session->userid,
            'fk_publisher' => $session->userid,
            'fk_user_last_editor' => $session->userid,
            
            'version'=> 0,
        );
        
        $data = $data + $defaults;
    }
    
    
    /**
     * Read page
     *
     * @param int $pk_page
     * @return Page|null
     */
    public function read($pk_page)
    {        
        $sql = 'SELECT * FROM `pages` WHERE `pk_page` = ?';
        $rs  = $this->conn->Execute($sql, array($pk_page));
        
        if(($rs !== false) && (!$rs->EOF)){
            $this->loadProperties($rs->fields);
        } else {
            return null;
        }
        
        return $this;
    }
    
    
    /**
     * Get page root
     * For better performance use PageManager::getRoot()
     *
     * @return Page|null    Return page if found it, otherwise null
     */
    public function getRoot()
    {
        $sql = 'SELECT pk_page FROM pages WHERE fk_page = 0 ORDER BY weight';
        $pk_page  = $this->conn->GetOne($sql);
        
        if($pk_page === false) {
            return null;
        }
        
        return new Page($pk_page);
    }
    
    
    /**
     * Update page values
     * 
     * @todo Implement class Params
     * @uses SqlHelper
     * @throws OptimisticLockingException
     * @param array $data
     * @param int $pk_page
     */
    public function update($data, $pk_page)
    {
        // TODO: validation
        $filter = 'pk_page = ' . $pk_page;
        $fields = array(
            'fk_page', 'title', 'menu_title', 'short_url',
            'slug', 'description', 'keywords',
            'grid', 'theme', 'params', 'status', 'type',
            'inline_styles',
            'fk_publisher', 'fk_user_last_editor',
            'starttime', 'endtime',
            'changed', 'published',
            'version',
        );
        
        $this->prepareData($data);
        
        // TODO: implement class Params
        $data['params'] = serialize($data['params']);
        
        // Check optimistic locking
        if( isset($data['version']) ) {
            if( !$this->isLastVersion($data['version'], $data['pk_page']) ) {
                throw new OptimisticLockingException();
            } else {
                // Increment version
                $data['version'] += 1;
            }
        }
        
        SqlHelper::bindAndUpdate('pages', $fields, $data, $filter, $this->conn);
    }
    
    
    /**
     * Delete a page,
     * page must not to have child pages
     *
     * Precondition:
     *   Page::hasChildPages($pk_page) == false
     * 
     * @param int $pk_page
     * @return boolean  Return true if page was removed
     */ 
    public function delete($pk_page)
    {
        if(!$this->hasChildPages()) {
            $sql = 'DELETE FROM pages WHERE pk_page = ?';
            
            $rs = $this->conn->Execute($sql, array($pk_page));
            if($rs === false) {
                throw new Exception( $conn->ErrorMsg() );
            }
        }
        
        return false;
    }
    
    
    /**
     * Use field version for optimistic locking
     *
     * @param int $version
     * @param int $pk_page
     * @return boolean
     */
    public function isLastVersion($version, $pk_page)
    {
        $sql = 'SELECT version FROM `pages` WHERE `pk_page` = ' . intval($pk_page);
        $currentVersion = $this->conn->GetOne($sql);
        
        return $currentVersion == $version;
    }    
    
    
    /**
     * Generate a unique slug 
     *
     * @uses Onm_Filter_Slug
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
     * Assemble route for this page
     * IMPORTANT: use only from FrontEnd, otherwise
     * FIXME: solve problem if this method called from backend
     *
     * @return string
     */
    public function getPermalink()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        
        $urlRoute = null;
        
        if($router->hasRoute('page-index')) {
            $route = $router->getRoute('page-index');
            $urlRoute = $route->assemble(array('slug' => $this->slug), true);
        }
        
        return $urlRoute;
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
    
    
    /**
     * Get a page by slug property
     *
     * TODO: evaluate to implement this method only in PageManager
     * @param string $slug
     * @return Page
     */
    public function getPageBySlug($slug)
    {        
        $sql = 'SELECT * FROM pages WHERE slug = ?';
        $rs  = $this->conn->Execute($sql, array($slug));
        
        $page = new Page();
        
        if( ($rs !== false) && (!$rs->EOF) ) {
            $page->loadProperties($rs->fields);
        }
        
        return $page;
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
        // O pai
        $sql = 'SELECT * FROM pages WHERE pk_page = ?';
        $rs  = $this->conn->Execute($sql, array($parent));
        
        // Added parent 
        $page = new Page();
        $this->loadProperties($rs->fields, $page);
        
        $tree = array();
        $tree['element'] = $page;
        
        $sql = 'SELECT * FROM pages WHERE fk_page = ? ORDER BY weight';
        $rs  = $this->conn->Execute($sql, array($parent));
        
        while(!$rs->EOF) {            
            $tree['childNodes'][] = $this->getTree($rs->fields['pk_page']);
            
            $rs->MoveNext();
        }
        
        return $tree;
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
        
        $sql = 'SELECT count(*) AS num_children FROM pages WHERE fk_page = ?';
        $rs = $this->conn->GetOne($sql, array($pk_page));
        
        if($rs === false) {
            throw new Exception( $conn->ErrorMsg() );
        }
        
        return intval($rs) > 0;
    }
    
    
    /**
     * Load values in associative array to current object ($this)
     * 
     * @param array $assocProps     Associative array 
     */
    public function loadProperties($assocProps, $object=null)
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
     * Utility method to recover filter condition to recover this object by ajax
     *
     * <code>
     * echo $content->getFilterStr();
     * // return: pk_page=5
     * </code>
     *
     * @return string
     */
    public function getFilterStr()
    {
        return 'pk_page=' . $this->pk_page;
    }
    
    
    /**
     * Change parent reference of page and weight param
     * Method used for jstree - http://www.jstree.com/
     * 
     * @deprecated
     * @param int $pk_page
     * @param int $fk_page
     * @param int $weight
     */
    public function moveNode($pk_page, $fk_page, $weight)
    {
        // FIXME: review funcionalities
        $sql = 'UPDATE pages SET fk_page=?, weight=? WHERE pk_page=?';
        $rs = $this->conn->Execute($sql, array($fk_page, $weight, $pk_page));
        
        if($rs === false) {
            
        }
        
        $sql = 'UPDATE pages SET weight = weight + 1 WHERE fk_page=? AND weight >= ? AND pk_page <> ?';
        
        $rs = $this->conn->Execute($sql, array($weight, $fk_page, $pk_page));
        if($rs === false) {
            
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

                $i++;
                
                $rs->MoveNext();
            }
        } else {
            $logger->log($this->conn->ErrorMsg(), Zend_Log::INFO);
        }
    }
    
    
    /**
     * Rename title of page
     * Method used for jstree - http://www.jstree.com/
     *
     * @deprecated
     * @param int $pk_page
     * @param string $title
     */
    public function renameNode($pk_page, $title)
    {
        $sql = 'UPDATE pages SET title=? WHERE pk_page=?';
        $rs = $this->conn->Execute($sql, array($title, $pk_page));
        if($rs === false) {
            // log
        }
    }
    
    
    /**
     * Process the page considering your type and status
     * 
     * @todo implement dispatch method using classes of page, for example PageShortcut
     * @throws PageNotAvailableException
     * @return mixed   Return HTML content of page or void
     */
    public function dispatch()
    {
        // Check page status
        if($this->status != 'AVAILABLE') {
            throw new PageNotAvailableException('Page ' . $this->title . ' is not available.');
        }
        
        $type = ucfirst(strtolower($this->type));
        $method = '_dispatch' . $type;
        
        if(method_exists($this, $method)) {
            $result = $this->{$method}();
        } else {
            throw new Exception('Not exists a valid mapping for type: ' . $type);
        }
        
        return $result;
    }
    
    
    /**
     * Dispatch a page of standard type
     *
     * @uses Page::render()
     * @return string
     */
    public function _dispatchStandard()
    {
        return $this->render();
    }
    
    
    /**
     * Dispatch a page of external type
     * redirect to external resource
     */
    public function _dispatchExternal()
    {
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
        
        $url = $this->getParam('url');
        
        if($url == null) {
            throw new Exception('Param «url» do not exists.');
        }
        
        $redirector->gotoUrl($url);
    }
    
    
    /**
     * Dispatch a page of type == SHORTCUT
     * redirect to other page
     *
     * @throws Exception
     */
    public function _dispatchShortcut()
    {
        $pk_page = $this->getParam('pk_page');
        
        $pageMgr = PageManager::getInstance();
        $page = $pageMgr->get($pk_page);
        
        if($page == null) {
            // TODO: custom exception
            throw new Exception('Param «pk_page» do not exists.');
        }
        
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
        $redirector->gotoRoute(
            array('slug' => $page->slug),
            'page-index'
        );
    }
    
    
    /**
     * Dispatch a page of type == NOT_IN_MENU
     * 
     * @uses Page::_dispatchStandard()
     * @return string
     */
    public function _dispatchNot_in_menu()
    {
        return $this->_dispatchStandard();
    }
    
    
    /**
     * Get param name
     *
     * @param string $paramName
     * @return mixed|null   Return param if exists otherwise return null
     */
    public function getParam($paramName)
    {
        $params = $this->getParams();
        
        if(isset($params[$paramName])) {
            return $params[$paramName];
        }
        
        return null;
    }
    
    
    /**
     * Get params for this page
     * 
     * @return array    Array of params
     */
    public function getParams()
    {
        $params = array();
        if($this->params != null) {
            $params = unserialize($this->params);
        }
        
        return $params;
    }
    
    
    public function setInner($content, $mask=null)
    {
        $this->_inner['content'] = $content;
        
        if($mask != null) {
            $this->_inner['mask'] = $mask;
        }
    }
    
    
    /**
     * Render $this page
     *
     * @return string
     */
    public function render()
    {
        if(!is_null($this->theme) && ($this->theme != TEMPLATE_USER)) {
            $tpl =& Zend_Registry::get('tpl');
            $tpl->setTheme($this->theme);
        }
        
        $grid = Grid::getInstance($this);
        
        $pageMgr = PageManager::getInstance();
        $items   = $pageMgr->getContentsByPage( $this->pk_page );
        
        $contents = array();
        foreach($items as $placeholder => $cts) {
            
            $contents[$placeholder] = array();
            
            foreach($cts as $it) {
                $content = Content::get($it['pk_content']);
                $common = array(
                    'page'        => $this,
                    'params'      => $it['params'],
                    'weight'      => $it['weight'],
                    'placeholder' => $it['placeholder'],
                );
                
                if($content instanceof Inner) {
                    if(isset($this->_inner['content'])) {
                        $props['content'] = $this->_inner['content'];
                        $props['mask'] = (isset($this->_inner['mask']))? $this->_inner['mask']: null;
                        
                        $props = $props + $common;
                        $box = new ContentBox($props);  
                        
                        $contents[$placeholder][] = $box;
                    }
                } else {
                    $props = array(
                        'content'     => $content,
                        'mask'        => $it['mask'],
                    );
                    
                    $props = $props + $common;
                    $box = new ContentBox($props);
                    
                    $contents[$placeholder][] = $box;
                }
            }
        }
        
        $output = $grid->render($contents);
        
        return $output;
    }
    
    
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

} // END: class Page