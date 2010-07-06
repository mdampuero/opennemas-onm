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
 * CategoryManager
 * 
 * @package    Core
 * @subpackage Content
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: category_manager.class.php 1 2010-05-18 11:38:48Z vifito $
 */
class CategoryManager
{
    /**
     * @var ContentCategoryManager instance, singleton pattern
     */
    static private $instance = null;
    
    /**
     * @var array with categories
     */
    public $categories = null;    
    
    /**
     * @var MethodCacheManager
     */    
    public $cache = null;
    
    /**
     * @var ADOConnection
     */    
    private $conn = null;


    /**
     * Construct
     * Implements a Singleton pattern
     *
     * @access private
     * @uses CategoryManager::populate()
     * @uses Zend_Registry::get()
     */
    private function __construct()
    {
        // Set ADOConnection
        if(Zend_Registry::isRegistered('conn')) {
            $this->conn = Zend_Registry::get('conn');
        }
        
        // Load all categories to improvement performance using cache
        // $this->cache = new MethodCacheManager($this, array('ttl' => 30));                        
        // $this->categories = $this->cache->populate();
        
        $this->categories = $this->populate();
    }
    
    
    /**
     * Get instance
     * Implements a Singleton pattern
     * 
     * @static
     * @access public
     * @uses   CategoryManager::$instance
     * @return CategoryManager
     */
    public static function getInstance()
    {
        if( is_null(self::$instance) ) {
            self::$instance = new CategoryManager();
        } 
        
        return self::$instance;       
    }     
    
    
    /**
     * Load internal array $this->categories for use singleton instance
     * Return value to use MethodCacheManager features
     *
     * @access public
     * @return array|null    Array with Category objects or null if error
    */
    public function populate()
    {
        $sql = 'SELECT * FROM `categories` ORDER BY `fk_category`, `title`';
        $rs  = $this->conn->Execute( $sql );
        
        if ($rs === false) {            
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return null;
        }
        
        // Clear previous categories
        $this->categories = array();        
        if($rs !== false) {
            while( $obj = $rs->FetchNextObject($toupper=false) ) {            
                $this->categories[ $obj->pk_category ] = $obj;
            }
        }        
        
        // Return internal array to use cache
        return $this->categories;
    }
    
    
    /**
     * Getter property categories
     *
     * @return array    Array of Category objects
    */
    public function getCategories()
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }
        
        return $this->categories;
    }
    
    
    /**
     * find objects of category and subcategory
     *
     * @param $filter - filter of sql
     * @param $order_by
     * @return array    Return category objects
    */
    public function find($filter=null, $_order_by='name')
    {
        $items = array();
        
        $sql = 'SELECT * FROM `categories` ORDER BY ' . $_order_by;
        if(!is_null($filter)) {
            $sql = 'SELECT * FROM `categories` ' .
                'WHERE ' . $filter . ' ORDER BY ' . $_order_by;
        }   
        
        $rs = $this->conn->Execute($sql);
        if($rs !== false) {
            while(!$rs->EOF) {                
                $obj = new Category();
                $obj->load($rs->fields);
                
                $items[] = $obj;
                
                $rs->MoveNext();
            }
        }
        
        return $items;
    }    
    
    
    /**
     * Get Category object by primary key
     * 
     * @throws CategoryManagerException
     * @param int $pk_category
     * @return Category
    */
    public function get($pk_category)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }
        
        if(isset($this->categories[$pk_category])) {
            return($this->categories[$pk_category]);
        } 
        
        return null;
    }
    
    
    /**
     * Get Category object by name
     * 
     * @throws CategoryManagerException
     * @param string $name
     * @return Category
    */
    public function getByName($name)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }
        
        foreach($this->categories as $category) {
            if($category->name == $name) {
                return $category;
            }
        }
    }
    
    
    /**
     * Get path
     *
     * @param int $pk_category
     * @return array
    */
    public function getPath($pk_category)
    {
        $path = array();
        $id = $pk_category;
        
        do {
            $category = $this->get($id);
            array_unshift($path, $category->title);
            
            $id = $category->fk_category;
        } while($id != 0);
        
        return $path;
    }
    
    
    /**
     * Get name of category
     * Deprecated, use get($pk_category) to retrieve object Category
     * 
     * @deprecated
     * @throws CategoryManagerException
     * @param int $pk_category
     * @return string   Name of category
    */
    public function getName($pk_category)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }
        
        if(isset($this->categories[$pk_category])
           && isset($this->categories[$pk_category]->name))
        {
            return($this->categories[$pk_category]->name);
        }
        
        return null;
    }
    
    
    /**
     * Get identifier (pk_category) of category using name
     * Deprecated, use getByName($name) to retrieve object Category
     * 
     * @deprecated
     * @throws CategoryManagerException
     * @param string $name
     * @return int|null  Primary key of category or null if it don't exists
    */
    public function getId($name)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }
        
        foreach($this->categories as $category) {
            if($category->name == $name) {
                return $category->pk_category;
            }
        }
        
        return null;
    }
    
    
    /**
     * Get title of category using pk_category
     * Deprecated, use getByName($name) to retrieve object Category
     * 
     * @deprecated
     * @throws CategoryManagerException
     * @param string $name
     * @return string|null  Return null if title don't exists
    */
    public function getTitle($pk_category)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }                
        
        foreach($this->categories as $category) {
            if($category->pk_category == $pk_category) {
                return $category->title;
            }
        }
        
        return null;
    }
    
    
    /**
     * Get title of category using name
     * Deprecated, use getByName($name) to retrieve object Category
     * 
     * @deprecated
     * @throws CategoryManagerException
     * @param string $name
     * @return string|null  Return null if title don't exists
    */
    public function getTitleByName($name)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }                
        
        foreach($this->categories as $category) {
            if($category->name == $name) {
                return $category->title;
            }
        }
        
        return null;
    }
    
    
    /**
     * Check if a name exists
     * 
     * @deprecated
     * @param string $name
     * @return boolean
    */
    public function exists($name)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }
        
        foreach($this->categories as $category) {
            if($category->name == $name) {
                return true;
            }
        }                        
        
        return false;
    }
    
    
    /**
     * Count items in category ($category) with content type equals than $type
     * 
     * Deprecated, use:
     * <code>
     * $categoryObject = new Category($valid_pk_category);
     * $total = $categoryObject->count('`fk_content_type`=' . $contentType);
     * </code>
     * 
     * @deprecated
     * @param int $category
     * @param int $type
     * @return int  Total of items in category for this type
     * 
    */
    public function countContentByType($category, $type)
    {
        $sql = 'SELECT count(pk_content) AS number
                FROM `contents`, `contents_categories`
                WHERE `contents`.`pk_content` = `contents_categories`.`pk_fk_content` AND
                      `contents_categories`.`pk_fk_category` = ? AND
                      `contents`.`fk_content_type` = ?';
        
        $rs = $this->conn->Execute( $sql, array($category, $type) );
        
        if($rs->fields['number']) {
            return $rs->fields['number'];
        } 
        
        return 0;
    }
    
    
    /**
     * Count items in categories for a specified content type
     * 
     * @deprecated
     * @see ContentCategoryManager::CountContentByType
     *
     * @param int $type
     * @param string $filter
     * @return array
    */
    public function countContentByTypeGroup($type, $filter=null)
    {
        $_where = '1=1';
        
        if( !is_null($filter) ) {
            $_where = $filter;
        }
        
        $sql = 'SELECT count(contents.pk_content) AS number, `contents_categories`.`pk_fk_content_category` AS cat
                FROM `contents`, `contents_categories`
                WHERE `contents`.`pk_content`=`contents_categories`.`pk_fk_content` AND
                      `contents`.`fk_content_type` = ? AND ' .
                      $_where . '
                GROUP BY `contents_categories`.`pk_fk_content_category`';
       
        $rs = $this->conn->Execute( $sql, array($type) );
        
        $groups = array();
        
        if($rs!==false) {
            while(!$rs->EOF) {
                $groups[ $rs->fields['cat'] ] = $rs->fields['number'];
                $rs->MoveNext();
            }
        }
        
        return $groups;
    }    
    
    
    /**
     *
     * @param array $pk_categories  Array of pk_category identifiers
    */
    public function detachContents($pk_categories)
    {
        $sql = 'DELETE FROM `contents_categories`
                WHERE `pk_fk_category` IN (' . implode(',', $pk_categories) . ')';
        
        $rs = $this->conn->Execute($sql);
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }
    
    
    /**
     * 
     *
     * @param int $pk_category
     * @return array    Array of affected pk_category identifiers
    */
    public function deleteRecursive($pk_category)
    {
        $descendants = $this->getDescendants($pk_category);
        
        $pks = array_merge(array($pk_category), $descendants);
        
        $sql = 'DELETE FROM `categories`
                WHERE pk_category IN (' . implode(',', $pks) . ')';
        
        $rs = $this->conn->Execute($sql);
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return $pks;
    }
    
    
    /**
     * Get an array of categories with parent category equals than $pk_category
     *
     * @throws CategoryManagerException
     * @param int $pk_category
     * @return array    Array of categories
    */
    public function getChildren($pk_category)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }
        
        $children = array();
        foreach($this->categories as $category) {
            if($category->fk_category == $pk_category) {
                $children[] = $category;
            }
        }
        
        return $children;
    }
    
    
    /**
     * Get parent category identifier
     *
     * @param int $pk_category
     * @return int
    */
    public function getParentId($pk_category)
    {
        if(is_null($this->categories)) {
            throw new CategoryManagerException('Categories should be preloaded');
        }
        
        $parent = null;
        foreach($this->categories as $category) {
            if($category->pk_category == $pk_category) {
                $parent = $category->fk_category;
            }
        }
        
        return $parent;
    }
    
    
    /**
     * Get recursive array from database
     * 
     * <code>
     * $tree = $catMgr->getTreeFromDb(0);
     * $html = $catMgr->tree2html($tree);
     * </code>
     * 
     * @see CategoryManager::tree2html()
     * @param int $parent
     * @return array
    */
    public function getTreeFromDb($parent=0)
    {                
        // Get parent
        $sql = 'SELECT * FROM `categories` WHERE `fk_category` = ?';
        $rs  = $this->conn->Execute($sql, array($parent));        
        
        $i = 0;
        $tree = array();
        
        while( !$rs->EOF ) {
            $category = new Category();
            $category->load($rs->fields);
            
            $tree[$i]['element'] = $category;
            $tree[$i]['childNodes'] = $this->getTreeFromDb($rs->fields['pk_category']);
            $i++;
            
            $rs->MoveNext();
        }                
        
        return $tree;
    }
    
    
    /**
     * TODO: parametrize template
    */
    public static function tree2html($root) {
        $html = '';
        
        if(!isset($root['element'])) {
            // is a childNodes array            
            $html .= '<ul>';            
            foreach($root as $tree) {                
                
                $html .= '<li id="category-' . $tree['element']->pk_category .'">';                
                $html .= '<a href="/categories/' . $tree['element']->name .'/">';
                $html .= $tree['element']->title;
                $html .= '</a>';
                
                if(isset($tree['childNodes'])) {
                    $html .= CategoryManager::tree2html($tree['childNodes']);
                }                
                
                $html .= '</li>';
                
            }            
            $html .= '</ul>';
            
        } 
        
        return $html;
    }
    
    
    /**
     * Get html options to build a Html select
     * 
     * <code>
     * // Get tree
     * $tree = $catMgr->getTree();
     *
     * // Get elements to disabled
     * $disabled = $catMgr->getDescendants(1);
     * 
     * echo('<select>');
     * echo $catMgr->getHtmlOptions(array('disabled' => $disabled, 'tabChar' => '&nbsp;&middot;&nbsp;'), $tree);
     * echo('</select>');
     * </code>
     *
     * @param array $options
     * @param array $tree
     * @param int $level
     * @return string
    */
    public function getHtmlOptions($options, $tree, $level=0)
    {        
        $html = '';
        
        foreach($tree as $category) {
            
            $html .= '<option value="' . $category['element']->pk_category . '"';
            
            if( isset($options['disabled']) 
                    && in_array($category['element']->pk_category, $options['disabled']) ) {                
                $html .= ' disabled="disabled"';
            }
            
            if( isset($options['className']) ) {
                $html .= ' class="' . $options['className'] . '"';
            }
            
            if( isset($options['selected'])
                    && ($options['selected'] == $category['element']->pk_category)) {
                $html .= ' selected="selected"';
            }
            
            $html .= '>';
            
            if( isset($options['tabChar']) ) {                
                $html .= str_repeat($options['tabChar'], $level);
            }
            
            $html .= $category['element']->title;
            $html .= '</option>';
            
            $html .= $this->getHtmlOptions($options, $category['childNodes'], $level+1);
        }
        
        return $html;
    }
    
    
    /**
     * Get descendants categories
     *
     * @param int $pk_category
     * @return array
     */
    public function getDescendants($pk_category)
    {
        $descendants = array();
        
        $childs = $this->getChildren($pk_category);        
        
        while(($category = array_shift($childs)) !== null) {
            $descendants[] = $category->pk_category;
            
            $descendants = array_merge($descendants, $this->getDescendants($category->pk_category));
        }
        
        return $descendants;
    }
    
    
    /**
     * Get tree of categories
     *
     * @param int $parent
     * @return array
     */
    public function getTree($parent = 0)
    {
        $tree = array();        
        
        $childs = $this->getChildren($parent);
        
        $i = 0;
        while(($category = array_shift($childs)) !== null) {
            $tree[$i]['element'] = $category;
            $tree[$i]['childNodes'] = $this->getTree($category->pk_category);
            
            $i++;
        }
        
        return $tree;
    }
    
} // END: class CategoryManager